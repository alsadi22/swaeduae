import React from "react";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import Home from "./pages/Home";
import Events from "./pages/Events";
import EventDetails from "./pages/EventDetails";
import About from "./pages/About";
import Login from "./pages/Login";
import Signup from "./pages/Signup";
import Profile from "./pages/Profile";
import AdminDashboard from "./pages/AdminDashboard";
import CMSDashboard from "./pages/CMSDashboard";
import AdminAdvanced from "./pages/AdminAdvanced";
import Analytics from "./pages/Analytics";
import Messaging from "./pages/Messaging";
import AdminUsers from "./pages/AdminUsers";
import AdminEvents from "./pages/AdminEvents";
export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/events" element={<Events />} />
        <Route path="/events/:id" element={<EventDetails />} />
        <Route path="/about" element={<About />} />
        <Route path="/login" element={<Login />} />
        <Route path="/signup" element={<Signup />} />
        <Route path="/profile" element={<Profile />} />
        <Route path="/admin" element={<AdminDashboard />} />
        <Route path="/cms" element={<CMSDashboard />} />
        <Route path="/admin-advanced" element={<AdminAdvanced />} />
        <Route path="/analytics" element={<Analytics />} />
        <Route path="/messaging" element={<Messaging />} />
        <Route path="/admin/users" element={<AdminUsers />} />
        <Route path="/admin/events" element={<AdminEvents />} />
      </Routes>
    </BrowserRouter>
  );
}
