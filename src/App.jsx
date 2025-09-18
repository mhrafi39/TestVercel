import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";

import LandingPage from "./pages/LandingPage";
import LoginPage from "./pages/LoginPage";
import SignupPage from "./pages/SignupPage";
import AboutUs from "./pages/AboutUs";

import "./App.css";

function App() {
  return (
    <Router>
      <main>
        <Routes>
          {/* Public Routes */}
          <Route path="/" element={<LandingPage />} />      
          <Route path="/login" element={<LoginPage />} />   
          <Route path="/signup" element={<SignupPage />} /> 
          <Route path="/about" element={<AboutUs />} />
          
          {/* Fallback Route */}
          <Route
            path="*"
            element={<h2 className="text-center mt-10">Page Not Found</h2>}
          />
        </Routes>
      </main>
    </Router>
  );
}

export default App;
