import React from "react";
export default function LanguageSwitcher() {
  return (
    <div className="flex justify-center gap-2 my-3">
      <button className="bg-blue-800 text-white px-3 py-1 rounded">العربية</button>
      <button className="bg-white border px-3 py-1 rounded">English</button>
    </div>
  );
}
