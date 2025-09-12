import React from "react";
import EventCard from "./EventCard";
export default function EventCarousel() {
  const events = [
    {
      title: "زيارة منزل المسنين",
      desc: "زيارة كبار السن من المواطنين بقيادة مستشفى طوان...",
      location: "ABU DHABI AL AIN",
      date: "Dec 01, 2024 - Dec 01, 2025",
      time: "8:00 صباحاً - 6:00 مساءً",
      category: "خدمة المجتمع",
      img: "/events/elderly.jpg",
      volunteers: 405
    },
    {
      title: "الرسم مع سلامة",
      desc: "يدعوكم فريق الهمة التطوعي...",
      location: "ABU DHABI AL AIN",
      date: "Jul 31 - Aug 21, 2025",
      time: "4:00 مساءً - 6:00 مساءً",
      category: "أصحاب الهمم",
      img: "/events/flags.jpg",
      volunteers: 30
    },
    {
      title: "متطوع متخصص - المجالات الرياضية والصحية",
      desc: "نبحث عن متطوعين في مجال الرياضة والصحة...",
      location: "ABU DHABI CITY",
      date: "Jan 1 - Dec 1, 2025",
      time: "9:00 صباحاً - 4:30 مساءً",
      category: "الرياضة",
      img: "/events/sport.jpg",
      volunteers: 300
    },
    {
      title: "مهرجان ليوا عجمان للرطب والعسل 2025",
      desc: "يدعوكم سبع الإمارات للتطوع...",
      location: "AJMAN CITY",
      date: "Jul 30 - Aug 6, 2025",
      time: "8:00 صباحاً - 10:00 مساءً",
      category: "خدمة المجتمع",
      img: "/events/honey.jpg",
      volunteers: 30
    }
  ];
  return (
    <section className="w-full max-w-7xl mx-auto mb-12 mt-10">
      <div className="flex gap-6 overflow-x-auto py-2">
        {events.map((ev, i) => (
          <EventCard key={i} {...ev} />
        ))}
      </div>
    </section>
  );
}
