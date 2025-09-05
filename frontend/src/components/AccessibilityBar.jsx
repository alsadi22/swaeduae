import React from "react";
export default function AccessibilityBar() {
  return (
    <div className="flex justify-center gap-3 my-3">
      <button className="bg-gray-200 px-3 py-1 rounded">+A</button>
      <button className="bg-gray-200 px-3 py-1 rounded">A</button>
      <button className="bg-gray-200 px-3 py-1 rounded">-A</button>
      <span>|</span>
      <button className="bg-gray-200 px-3 py-1 rounded">تباين قوي</button>
      <button className="bg-gray-200 px-3 py-1 rounded">ألوان</button>
    </div>
  );
}
