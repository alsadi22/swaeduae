import React from "react";
import { FaFacebook, FaTwitter, FaInstagram, FaYoutube } from "react-icons/fa";
export default function SocialLinks() {
  return (
    <div className="flex gap-4 justify-center my-6">
      <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" className="text-xl text-blue-700 hover:text-blue-800"><FaFacebook /></a>
      <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" className="text-xl text-blue-500 hover:text-blue-700"><FaTwitter /></a>
      <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" className="text-xl text-pink-600 hover:text-pink-800"><FaInstagram /></a>
      <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" className="text-xl text-red-600 hover:text-red-800"><FaYoutube /></a>
    </div>
  );
}
