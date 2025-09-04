import React from "react";
import AppDownloadBar from "./AppDownloadBar";
import AccessibilityBar from "./AccessibilityBar";
import LanguageSwitcher from "./LanguageSwitcher";
import SocialLinks from "./SocialLinks";
export default function Footer() {
  return (
    <div className="mt-16 bg-blue-900 text-white pt-8 pb-2">
      <AppDownloadBar />
      <AccessibilityBar />
      <LanguageSwitcher />
      <SocialLinks />
      <div className="text-center py-2 text-xs">جميع الحقوق محفوظة © متطوعي الإمارات 2025</div>
    </div>
  );
}
