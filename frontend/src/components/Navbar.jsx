import React, { useContext } from 'react';
import { Link } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';

export default function Navbar() {
  const { user, logout } = useContext(AuthContext);

  return (
    <nav className="bg-blue-700 text-white p-4 flex justify-between">
      <div className="space-x-4">
        <Link to="/" className="font-bold">Home</Link>
        {user ? (
          <>
            <Link to="/dashboard" className="hover:underline">Dashboard</Link>
            <button onClick={logout} className="hover:underline">Logout</button>
          </>
        ) : (
          <>
            <Link to="/login" className="hover:underline">Login</Link>
          </>
        )}
      </div>
    </nav>
  );
}
