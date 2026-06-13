"""
TravelMate AI Service — Stable Gemini 2.5 Version (April 2026)
Uses Google Gemini via OpenAI-compatible SDK
"""

import os
import time
import logging
from functools import wraps

import pymysql
from dbutils.pooled_db import PooledDB
from flask import Flask, request, jsonify, g
from flask_cors import CORS
from flask_limiter import Limiter
from flask_limiter.util import get_remote_address
from openai import OpenAI
from dotenv import load_dotenv

# ─────────────────────────────────────────────
# Bootstrap & Config
# ─────────────────────────────────────────────
load_dotenv()

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s  %(levelname)-8s  %(name)s — %(message)s",
)
logger = logging.getLogger("travelmate.ai")

ALLOWED_ORIGINS = os.getenv("ALLOWED_ORIGINS", "*").split(",")
GEMINI_API_KEY  = os.getenv("GEMINI_API_KEY", "")   # Make sure this is set in .env

# Best model for TravelMate (fast + intelligent)
GEMINI_MODEL = "gemini-2.5-flash"
# Alternatives (uncomment if needed):
# GEMINI_MODEL = "gemini-2.5-flash-lite"   # Faster and cheaper
# GEMINI_MODEL = "gemini-flash-latest"     # Always points to newest Flash

DB_CONFIG = dict(
    host     = os.getenv("DB_HOST", "localhost"),
    user     = os.getenv("DB_USER", "root"),
    password = os.getenv("DB_PASS", ""),
    database = os.getenv("DB_NAME", "tms"),
    charset  = "utf8mb4",
    cursorclass = pymysql.cursors.DictCursor,
)

app = Flask(__name__)
CORS(app, origins=ALLOWED_ORIGINS)

limiter = Limiter(
    key_func=get_remote_address,
    app=app,
    default_limits=["200 per day", "50 per hour"],
    storage_uri="memory://",
)

# ─────────────────────────────────────────────
# Database — Connection Pool
# ─────────────────────────────────────────────
_pool: PooledDB | None = None

def get_pool() -> PooledDB:
    global _pool
    if _pool is None:
        _pool = PooledDB(
            creator=pymysql,
            maxconnections=20,
            mincached=2,
            maxcached=10,
            blocking=True,
            **DB_CONFIG,
        )
    return _pool

def get_db():
    if "db" not in g:
        g.db = get_pool().connection()
    return g.db

@app.teardown_appcontext
def close_db(exc=None):
    db = g.pop("db", None)
    if db is not None:
        db.close()

# ─────────────────────────────────────────────
# AI Client — Gemini via OpenAI Compatibility
# ─────────────────────────────────────────────
gemini_client = OpenAI(
    api_key=GEMINI_API_KEY,
    base_url="https://generativelanguage.googleapis.com/v1beta/openai",
    timeout=90,
)

def ask_gemini(
    messages: list[dict],
    *,
    max_tokens: int = 900,
    temperature: float = 0.75,
    max_retries: int = 4,
) -> str:
    for attempt in range(max_retries):
        try:
            resp = gemini_client.chat.completions.create(
                model=GEMINI_MODEL,
                messages=messages,
                temperature=temperature,
                max_tokens=max_tokens,
            )
            return resp.choices[0].message.content.strip()

        except Exception as exc:
            logger.error(f"AI Error (Attempt {attempt+1}/{max_retries}): {type(exc).__name__} - {exc}")

            if attempt < max_retries - 1:
                wait_time = min(2 ** attempt, 8)   # Exponential backoff (max 8s)
                time.sleep(wait_time)
            else:
                return "Sorry, the travel assistant is quite busy right now. Please try again in a few seconds."

    return "Service temporarily unavailable. Please try again later."

# ─────────────────────────────────────────────
# Helpers & Auth
# ─────────────────────────────────────────────
INTERNAL_API_KEY = os.getenv("INTERNAL_API_KEY", "")

def require_api_key(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        if not INTERNAL_API_KEY:
            return f(*args, **kwargs)
        auth = request.headers.get("Authorization", "")
        if not auth.startswith("Bearer ") or auth[7:] != INTERNAL_API_KEY:
            return jsonify({"error": "Unauthorized"}), 401
        return f(*args, **kwargs)
    return decorated

def _fetch_packages(limit: int = 12) -> list[dict]:
    conn = get_db()
    with conn.cursor() as cur:
        cur.execute(
            """SELECT PackageName, PackageLocation, PackagePrice,
                      PackageType, duration
               FROM tbltourpackages
               LIMIT %s""",
            (limit,),
        )
        return cur.fetchall()

def _packages_to_text(packages: list[dict]) -> str:
    if not packages:
        return "No packages currently available."
    return "\n".join(
        f"- {p['PackageName']} | {p['PackageLocation']} | "
        f"₹{p['PackagePrice']} | {p['PackageType']} | {p.get('duration', 'N/A')} days"
        for p in packages
    )

# ─────────────────────────────────────────────
# Routes
# ─────────────────────────────────────────────

@app.route("/")
def health():
    return jsonify({
        "status": "AI Service running ✅",
        "model": GEMINI_MODEL,
        "message": "Ready for travel queries!"
    })

@app.route("/chat", methods=["POST"])
@limiter.limit("30 per minute")
def chat():
    data     = request.get_json(silent=True) or {}
    user_msg = data.get("message", "").strip()

    if not user_msg:
        return jsonify({"error": "Message cannot be empty"}), 400

    try:
        packages = _fetch_packages(12)
    except Exception as exc:
        logger.error("DB error in /chat: %s", exc)
        packages = []

    system_prompt = f"""You are a friendly, helpful, and enthusiastic travel assistant for TravelMate Tourism.
Your goal is to help users explore tour packages, plan exciting trips, suggest destinations, itineraries, and answer all travel-related questions.

Keep your replies warm, engaging, and concise. Use bullet points or numbered lists when suggesting options or plans.
Be helpful and suggest relevant packages when possible.

Current available packages:
{_packages_to_text(packages)}"""

    # Support optional conversation history for multi-turn chat
    # history = [ {"role": "user"|"assistant", "content": "..."}, ... ]
    history = data.get("history", [])
    if not isinstance(history, list):
        history = []

    # Sanitise: only valid roles, cap at last 10 turns to stay within token limits
    safe_history = [
        {"role": h["role"], "content": str(h["content"])[:500]}
        for h in history[-10:]
        if isinstance(h, dict) and h.get("role") in ("user", "assistant") and h.get("content")
    ]

    messages = (
        [{"role": "system", "content": system_prompt}]
        + safe_history
        + [{"role": "user", "content": user_msg}]
    )

    reply = ask_gemini(messages)
    return jsonify({"reply": reply})

@app.route("/analytics", methods=["GET"])
@require_api_key
def analytics():
    """
    Returns booking analytics + an AI-generated business insight.
    Called by admin/ai_analytics.php
    """
    try:
        conn = get_db()
        with conn.cursor() as cur:

            # ── 1. Monthly bookings for last 6 months ──────────────────────
            cur.execute("""
                SELECT DATE_FORMAT(BookingDate, '%b %Y') AS month,
                       COUNT(*)                          AS bookings
                FROM   tblbookingdetails
                WHERE  BookingDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP  BY DATE_FORMAT(BookingDate, '%Y-%m')
                ORDER  BY MIN(BookingDate) ASC
            """)
            monthly_bookings = cur.fetchall()

            # ── 2. Top 5 most-booked packages ─────────────────────────────
            cur.execute("""
                SELECT p.PackageName,
                       COUNT(b.id) AS total_bookings
                FROM   tblbookingdetails  b
                JOIN   tbltourpackages    p ON b.PackageId = p.PackageId
                GROUP  BY b.PackageId
                ORDER  BY total_bookings DESC
                LIMIT  5
            """)
            top_packages = cur.fetchall()

            # ── 3. Average price & count by package type ──────────────────
            cur.execute("""
                SELECT PackageType,
                       ROUND(AVG(PackagePrice), 0) AS avg_price,
                       COUNT(*)                    AS count
                FROM   tbltourpackages
                GROUP  BY PackageType
                ORDER  BY avg_price DESC
            """)
            by_type = cur.fetchall()

            # ── 4. Quick summary stats for the AI prompt ──────────────────
            cur.execute("SELECT COUNT(*) AS total FROM tblbookingdetails")
            total_bookings = (cur.fetchone() or {}).get("total", 0)

            cur.execute("SELECT COUNT(*) AS total FROM tbltourpackages")
            total_packages = (cur.fetchone() or {}).get("total", 0)

            cur.execute("""
                SELECT COUNT(*) AS total
                FROM   tblbookingdetails
                WHERE  BookingDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            """)
            recent_bookings = (cur.fetchone() or {}).get("total", 0)

        # ── 5. Build AI insight ────────────────────────────────────────────
        top_names = ", ".join(p["PackageName"] for p in top_packages[:3]) if top_packages else "N/A"
        type_summary = "; ".join(
            f"{t['PackageType']} (avg ₹{t['avg_price']}, {t['count']} pkgs)"
            for t in by_type
        ) if by_type else "N/A"

        ai_prompt = f"""You are a travel business analyst. Give a concise (4–6 sentences) strategic insight for a tourism manager based on this data:

- Total bookings ever: {total_bookings}
- Bookings in the last 30 days: {recent_bookings}
- Total active packages: {total_packages}
- Top booked packages: {top_names}
- Package types with avg prices: {type_summary}

Focus on trends, what's working, and one actionable recommendation. Be direct and professional."""

        ai_insight = ask_gemini(
            [{"role": "user", "content": ai_prompt}],
            max_tokens=400,
            temperature=0.6,
        )

        return jsonify({
            "monthly_bookings": monthly_bookings,
            "top_packages":     top_packages,
            "by_type":          by_type,
            "ai_insight":       ai_insight,
            "summary": {
                "total_bookings":  total_bookings,
                "total_packages":  total_packages,
                "recent_bookings": recent_bookings,
            },
        })

    except Exception as exc:
        logger.error("Analytics error: %s", exc)
        return jsonify({"error": str(exc)}), 500


@app.route("/recommend", methods=["POST"])
def recommend():
    data = request.get_json(silent=True) or {}
    try:
        pkg_id = int(data.get("package_id") or 0)
        pkg_type = str(data.get("package_type", ""))[:100]
        location = str(data.get("location", ""))[:200]
        location_prefix = location.split(",")[0].strip() if location else ""
        conn = get_db()
        with conn.cursor() as cur:
            cur.execute(
                """SELECT PackageId, PackageName, PackageLocation, PackagePrice,
                          PackageType, PackageImage, duration
                   FROM tbltourpackages
                   WHERE PackageId != %s
                     AND (PackageType = %s OR PackageLocation LIKE %s)
                   LIMIT 5""",
                (pkg_id, pkg_type, f"%{location_prefix}%"),
            )
            results = cur.fetchall()
        return jsonify({"recommendations": results})
    except Exception as exc:
        logger.error("Recommend error: %s", exc)
        return jsonify({"error": str(exc)}), 500

if __name__ == "__main__":
    print(f"🚀 TravelMate AI Service Active | Model: {GEMINI_MODEL}")
    print("   Warming up Gemini model... Please wait a moment.")
    time.sleep(3)   # Small delay to reduce initial 503 errors

    app.run(host="0.0.0.0", port=5000, debug=True)