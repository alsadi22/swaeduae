<?xml version="1.0" encoding="UTF-8"?>
<svg width="1600" height="900" viewBox="0 0 1600 900" xmlns="http://www.w3.org/2000/svg">
  <style>
    .txt { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; fill: #0f172a; font-weight: 600; }
    .cap { font-size: 22px; font-weight: 700; }
    .sm  { font-size: 16px; font-weight: 600; }
    .box { rx="12" }
    .line{ stroke:#94a3b8; stroke-width:2 }
    .b0{ fill:#fff; stroke-width:3 }
    .c-home{ stroke:#6292d1 }
    .c-opps{ stroke:#3b82f6 }
    .c-orgs{ stroke:#8b5cf6 }
    .c-certs{ stroke:#10b981 }
    .c-cont{ stroke:#f97316 }
    .c-supp{ stroke:#ef4444 }
    .c-auth{ stroke:#0ea5e9 }
    .c-prof{ stroke:#22c55e }
    .c-foot{ stroke:#64748b }
  </style>

  <!-- helper -->
  <defs>
    <rect id="box" width="240" height="60" rx="12" fill="#fff"/>
    <rect id="boxS" width="200" height="48" rx="12" fill="#fff"/>
    <rect id="boxXS" width="160" height="40" rx="12" fill="#fff"/>
  </defs>

  <!-- Homepage -->
  <use href="#box" x="680" y="40" class="b0 c-home"/>
  <text x="800" y="78" text-anchor="middle" class="txt cap">Homepage</text>

  <!-- Top sections -->
  <use href="#box" x="120" y="140" class="b0 c-opps"/><text x="240" y="178" text-anchor="middle" class="txt cap">Opportunities</text>
  <use href="#box" x="420" y="140" class="b0 c-orgs"/><text x="540" y="178" text-anchor="middle" class="txt cap">Organizations</text>
  <use href="#box" x="720" y="140" class="b0 c-certs"/><text x="840" y="178" text-anchor="middle" class="txt cap">Certificates</text>
  <use href="#box" x="1020" y="140" class="b0 c-cont"/><text x="1140" y="178" text-anchor="middle" class="txt cap">Content &amp; Media</text>

  <!-- Middle row -->
  <use href="#box" x="220" y="260" class="b0 c-supp"/><text x="340" y="298" text-anchor="middle" class="txt cap">Support &amp; Legal</text>
  <use href="#box" x="620" y="260" class="b0 c-auth"/><text x="740" y="298" text-anchor="middle" class="txt cap">Auth</text>
  <use href="#box" x="920" y="260" class="b0 c-prof"/><text x="1040" y="298" text-anchor="middle" class="txt cap">Volunteer Profile (login)</text>
  <use href="#box" x="1320" y="260" class="b0 c-foot"/><text x="1440" y="298" text-anchor="middle" class="txt cap">Footer</text>

  <!-- Lines from homepage -->
  <line x1="800" y1="100" x2="240" y2="140" class="line"/>
  <line x1="800" y1="100" x2="540" y2="140" class="line"/>
  <line x1="800" y1="100" x2="840" y2="140" class="line"/>
  <line x1="800" y1="100" x2="1140" y2="140" class="line"/>
  <line x1="800" y1="100" x2="340" y2="260" class="line"/>
  <line x1="800" y1="100" x2="740" y2="260" class="line"/>
  <line x1="800" y1="100" x2="1040" y2="260" class="line"/>
  <line x1="800" y1="100" x2="1440" y2="260" class="line"/>

  <!-- Opportunities children -->
  <use href="#boxS" x="140" y="220" class="b0 c-opps"/><text x="240" y="250" text-anchor="middle" class="txt sm">List + Filters</text>
  <use href="#boxS" x="140" y="280" class="b0 c-opps"/><text x="240" y="310" text-anchor="middle" class="txt sm">Details (Apply)</text>
  <line x1="240" y1="200" x2="240" y2="220" class="line" style="stroke:#3b82f6"/>
  <line x1="240" y1="200" x2="240" y2="280" class="line" style="stroke:#3b82f6"/>

  <!-- Organizations children -->
  <use href="#boxS" x="440" y="220" class="b0 c-orgs"/><text x="540" y="250" text-anchor="middle" class="txt sm">Directory</text>
  <use href="#boxS" x="440" y="280" class="b0 c-orgs"/><text x="540" y="310" text-anchor="middle" class="txt sm">Profile</text>
  <line x1="540" y1="200" x2="540" y2="220" class="line" style="stroke:#8b5cf6"/>
  <line x1="540" y1="200" x2="540" y2="280" class="line" style="stroke:#8b5cf6"/>

  <!-- Certificates children -->
  <use href="#boxS" x="740" y="220" class="b0 c-certs"/><text x="840" y="250" text-anchor="middle" class="txt sm">Verify (code → result)</text>
  <line x1="840" y1="200" x2="840" y2="220" class="line" style="stroke:#10b981"/>

  <!-- Content & Media children -->
  <use href="#boxS" x="1040" y="220" class="b0 c-cont"/><text x="1140" y="250" text-anchor="middle" class="txt sm">News — Index</text>
  <use href="#boxS" x="1040" y="280" class="b0 c-cont"/><text x="1140" y="310" text-anchor="middle" class="txt sm">News — Article</text>
  <use href="#boxS" x="1040" y="340" class="b0 c-cont"/><text x="1140" y="370" text-anchor="middle" class="txt sm">Gallery</text>
  <use href="#boxS" x="1040" y="400" class="b0 c-cont"/><text x="1140" y="430" text-anchor="middle" class="txt sm">Blog</text>
  <use href="#boxS" x="1040" y="460" class="b0 c-cont"/><text x="1140" y="490" text-anchor="middle" class="txt sm">Stories</text>
  <use href="#boxS" x="1040" y="520" class="b0 c-cont"/><text x="1140" y="550" text-anchor="middle" class="txt sm">Partners</text>
  <line x1="1140" y1="200" x2="1140" y2="220" class="line" style="stroke:#f97316"/>
  <line x1="1140" y1="200" x2="1140" y2="280" class="line" style="stroke:#f97316"/>
  <line x1="1140" y1="200" x2="1140" y2="340" class="line" style="stroke:#f97316"/>
  <line x1="1140" y1="200" x2="1140" y2="400" class="line" style="stroke:#f97316"/>
  <line x1="1140" y1="200" x2="1140" y2="460" class="line" style="stroke:#f97316"/>
  <line x1="1140" y1="200" x2="1140" y2="520" class="line" style="stroke:#f97316"/>
  <line x1="1140" y1="250" x2="1140" y2="280" class="line" style="stroke:#f97316"/>

  <!-- Support & Legal children -->
  <use href="#boxXS" x="245" y="340" class="b0 c-supp"/><text x="325" y="365" text-anchor="middle" class="txt sm">About</text>
  <use href="#boxXS" x="365" y="340" class="b0 c-supp"/><text x="445" y="365" text-anchor="middle" class="txt sm">Contact</text>
  <use href="#boxXS" x="485" y="340" class="b0 c-supp"/><text x="565" y="365" text-anchor="middle" class="txt sm">FAQ</text>
  <use href="#boxXS" x="245" y="392" class="b0 c-supp"/><text x="325" y="417" text-anchor="middle" class="txt sm">Toolkits</text>
  <use href="#boxXS" x="365" y="392" class="b0 c-supp"/><text x="445" y="417" text-anchor="middle" class="txt sm">Research</text>
  <use href="#boxXS" x="485" y="392" class="b0 c-supp"/><text x="565" y="417" text-anchor="middle" class="txt sm">Terms</text>
  <use href="#boxXS" x="245" y="444" class="b0 c-supp"/><text x="325" y="469" text-anchor="middle" class="txt sm">Privacy</text>
  <use href="#boxXS" x="365" y="444" class="b0 c-supp"/><text x="445" y="469" text-anchor="middle" class="txt sm">Financials</text>
  <use href="#boxXS" x="485" y="444" class="b0 c-supp"/><text x="565" y="469" text-anchor="middle" class="txt sm">Team</text>
  <line x1="340" y1="320" x2="325" y2="340" class="line" style="stroke:#ef4444"/>
  <line x1="340" y1="320" x2="445" y2="340" class="line" style="stroke:#ef4444"/>
  <line x1="340" y1="320" x2="565" y2="340" class="line" style="stroke:#ef4444"/>
  <line x1="340" y1="320" x2="325" y2="392" class="line" style="stroke:#ef4444"/>
  <line x1="340" y1="320" x2="445" y2="392" class="line" style="stroke:#ef4444"/>
  <line x1="340" y1="320" x2="565" y2="392" class="line" style="stroke:#ef4444"/>
  <line x1="340" y1="320" x2="325" y2="444" class="line" style="stroke:#ef4444"/>
  <line x1="340" y1="320" x2="445" y2="444" class="line" style="stroke:#ef4444"/>
  <line x1="340" y1="320" x2="565" y2="444" class="line" style="stroke:#ef4444"/>

  <!-- Auth -->
  <use href="#boxS" x="650" y="340" class="b0 c-auth"/><text x="750" y="370" text-anchor="middle" class="txt sm">Sign In (Volunteer/Org)</text>
  <use href="#boxS" x="650" y="392" class="b0 c-auth"/><text x="750" y="422" text-anchor="middle" class="txt sm">Sign Up (Volunteer/Org)</text>
  <use href="#boxS" x="650" y="444" class="b0 c-auth"/><text x="750" y="474" text-anchor="middle" class="txt sm">Forgot Password</text>
  <line x1="740" y1="320" x2="750" y2="340" class="line" style="stroke:#0ea5e9"/>
  <line x1="740" y1="320" x2="750" y2="392" class="line" style="stroke:#0ea5e9"/>
  <line x1="740" y1="320" x2="750" y2="444" class="line" style="stroke:#0ea5e9"/>

  <!-- Volunteer Profile -->
  <use href="#boxS" x="950" y="340" class="b0 c-prof"/><text x="1050" y="370" text-anchor="middle" class="txt sm">My Profile</text>
  <use href="#boxS" x="950" y="392" class="b0 c-prof"/><text x="1050" y="422" text-anchor="middle" class="txt sm">Applications</text>
  <use href="#boxS" x="950" y="444" class="b0 c-prof"/><text x="1050" y="474" text-anchor="middle" class="txt sm">Volunteer Hours</text>
  <line x1="1040" y1="320" x2="1050" y2="340" class="line" style="stroke:#22c55e"/>
  <line x1="1040" y1="320" x2="1050" y2="392" class="line" style="stroke:#22c55e"/>
  <line x1="1040" y1="320" x2="1050" y2="444" class="line" style="stroke:#22c55e"/>

  <!-- Footer links -->
  <use href="#boxXS" x="1360" y="340" class="b0 c-foot"/><text x="1440" y="365" text-anchor="middle" class="txt sm">Our Mission</text>
  <use href="#boxXS" x="1520" y="340" class="b0 c-foot"/><text x="1600" y="365" text-anchor="middle" class="txt sm">Team</text>
  <use href="#boxXS" x="1360" y="392" class="b0 c-foot"/><text x="1440" y="417" text-anchor="middle" class="txt sm">Partners</text>
  <use href="#boxXS" x="1520" y="392" class="b0 c-foot"/><text x="1600" y="417" text-anchor="middle" class="txt sm">Financials</text>
  <use href="#boxXS" x="1360" y="444" class="b0 c-foot"/><text x="1440" y="469" text-anchor="middle" class="txt sm">FAQ</text>
  <use href="#boxXS" x="1520" y="444" class="b0 c-foot"/><text x="1600" y="469" text-anchor="middle" class="txt sm">Blog</text>
  <use href="#boxXS" x="1360" y="496" class="b0 c-foot"/><text x="1440" y="521" text-anchor="middle" class="txt sm">Stories</text>
  <use href="#boxXS" x="1520" y="496" class="b0 c-foot"/><text x="1600" y="521" text-anchor="middle" class="txt sm">Toolkits</text>
  <use href="#boxXS" x="1440" y="548" class="b0 c-foot"/><text x="1520" y="573" text-anchor="middle" class="txt sm">Research</text>
  <line x1="1440" y1="320" x2="1440" y2="340" class="line" style="stroke:#64748b"/>
  <line x1="1440" y1="320" x2="1600" y2="340" class="line" style="stroke:#64748b"/>
  <line x1="1440" y1="320" x2="1440" y2="392" class="line" style="stroke:#64748b"/>
  <line x1="1440" y1="320" x2="1600" y2="392" class="line" style="stroke:#64748b"/>
  <line x1="1440" y1="320" x2="1440" y2="444" class="line" style="stroke:#64748b"/>
  <line x1="1440" y1="320" x2="1600" y2="444" class="line" style="stroke:#64748b"/>
  <line x1="1440" y1="320" x2="1440" y2="496" class="line" style="stroke:#64748b"/>
  <line x1="1440" y1="320" x2="1600" y2="496" class="line" style="stroke:#64748b"/>
  <line x1="1440" y1="320" x2="1520" y2="548" class="line" style="stroke:#64748b"/>
</svg>

