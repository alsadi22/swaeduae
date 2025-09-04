import React, { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
export default function NewsDetail() {
  const { id } = useParams();
  const [article, setArticle] = useState(null);
  useEffect(()=>{ fetch(`/api/news/${id}`).then(r=>r.json()).then(setArticle); },[id]);
  if (!article) return <div className="mt-20 text-center">جاري التحميل...</div>;
  return (
    <div className="max-w-3xl mx-auto mt-10 bg-white rounded-2xl p-8 shadow-xl">
      <Link to="/news" className="text-blue-600 underline mb-4 block">جميع الأخبار</Link>
      <h1 className="text-2xl font-bold mb-2">{article.title}</h1>
      <div className="text-gray-400 mb-4">{article.date}</div>
      <img src={article.image || "/img/news_placeholder.jpg"} className="w-full rounded-xl mb-6"/>
      <div>{article.content}</div>
    </div>
  );
}
