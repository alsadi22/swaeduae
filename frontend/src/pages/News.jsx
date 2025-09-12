import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
export default function News() {
  const [articles, setArticles] = useState([]);
  useEffect(()=>{ fetch("/api/news").then(r=>r.json()).then(setArticles); }, []);
  return (
    <div className="max-w-4xl mx-auto mt-10">
      <h1 className="text-3xl font-bold mb-6">الأخبار والفعاليات</h1>
      <div className="space-y-8">
        {articles.map(a=>(
          <Link to={`/news/${a.id}`} key={a.id} className="block bg-white rounded-2xl p-6 shadow hover:scale-105 duration-100">
            <div className="flex items-center gap-4">
              <img src={a.image || "/img/news_placeholder.jpg"} className="w-20 h-20 rounded-xl object-cover"/>
              <div>
                <div className="font-bold text-lg">{a.title}</div>
                <div className="text-gray-400 text-sm">{a.date}</div>
              </div>
            </div>
            <div className="mt-2">{a.summary}</div>
          </Link>
        ))}
      </div>
    </div>
  );
}
