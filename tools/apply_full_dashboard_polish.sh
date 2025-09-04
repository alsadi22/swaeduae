#!/usr/bin/env bash
set -euo pipefail

BRAND="public/css/brand.css"
mkdir -p "$(dirname "$BRAND")"
touch "$BRAND"
cp "$BRAND" "${BRAND}.bak_$(date +%F_%H%M%S)" || true

# Remove any previous polish block between the markers (idempotent)
awk '
  /\/\* dashboard-polish:start \*\// {skip=1}
  !skip {print}
  /\/\* dashboard-polish:end \*\// {skip=0}
' "$BRAND" > "$BRAND.__tmp__" 2>/dev/null || true
mv "$BRAND.__tmp__" "$BRAND" 2>/dev/null || true

# Append fresh polish block
cat >> "$BRAND" <<'CSS'
/* dashboard-polish:start */
/* Light, modern look to match the mock */

:root{
  --polish-card-radius: 16px;
  --polish-card-shadow: 0 6px 18px rgba(15, 23, 42, .06);
  --polish-soft-shadow: 0 3px 10px rgba(15, 23, 42, .06);
  --polish-border: 1px solid rgba(15, 23, 42, .06);
  --polish-muted: #6b7280;
}

/* Cards */
.card{
  border-radius: var(--polish-card-radius) !important;
  box-shadow: var(--polish-card-shadow) !important;
  border: var(--polish-border) !important;
  overflow: hidden;
}
.card .card-body{
  padding: 1.15rem 1.25rem !important;
}

/* KPI tiles — bolder numbers, compact headers */
.card h6,
.card .h6{ font-weight: 700; letter-spacing: .2px; margin-bottom: .35rem !important; }
.card .h2,
.card .h3{ font-weight: 800; letter-spacing: .2px; }

/* Top “pills” row */
.btn, .badge{
  border-radius: 12px !important;
}
.btn{ box-shadow: var(--polish-soft-shadow); }
.btn:hover{ transform: translateY(-1px); transition: transform .15s ease; }

/* Softer outlines + neutral hover */
.btn-outline-secondary,
.btn-outline-primary,
.btn-light{
  background: #fff;
  border-color: rgba(15, 23, 42, .12) !important;
}
.btn-outline-secondary:hover,
.btn-outline-primary:hover,
.btn-light:hover{
  background: #f8fafc !important;
}

/* Clean bullet lists used in side blocks */
.list-clean{ list-style: none; margin:0; padding-left: 0; }
.list-clean li + li{ margin-top: .4rem; }
.small, .text-muted{ color: var(--polish-muted) !important; }

/* Subtle colored dot/badge seen in the mock */
.badge.bg-primary-subtle{
  background: rgba(59,130,246,.12) !important;
  color: #1e40af !important;
}

/* Charts — force tidy, consistent height to avoid huge empty blocks */
#appsAttendChart,
#hoursChart{
  display: block;
  width: 100% !important;
  height: 240px !important;   /* match mock */
}

/* Spacing between big sections (graph band + side cards) */
.container-fluid .card{ margin-bottom: 1.0rem; }

/* Make the off-canvas/side nav content edge a touch softer on wide screens */
@media (min-width: 1200px){
  .main-content{ border-radius: 14px; }
}
/* dashboard-polish:end */
CSS

echo "✅ Polished CSS written to $BRAND"
echo "Tip: hard-refresh your browser (Ctrl/Cmd+Shift+R) to bust cache."
