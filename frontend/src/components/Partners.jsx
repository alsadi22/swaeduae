import React from "react";
export default function Partners() {
  return (
    <section className="py-8 w-full bg-blue-50 border-t border-blue-100 mt-8">
      <div className="container mx-auto flex flex-wrap items-center justify-center gap-10">
        <img src="/partners/mocd.png" alt="MOCD" className="h-12 grayscale hover:grayscale-0" />
        <img src="/partners/emirates_foundation.png" alt="Emirates Foundation" className="h-12 grayscale hover:grayscale-0" />
        <img src="/partners/red_crescent.png" alt="Red Crescent" className="h-12 grayscale hover:grayscale-0" />
        <img src="/partners/uae_aid.png" alt="UAE AID" className="h-12 grayscale hover:grayscale-0" />
      </div>
    </section>
  );
}
