import React from "react";

const LandingPage = () => {
  return (
    <div style={{ 
      width: "100vw", 
      height: "100vh", 
      display: "flex", 
      alignItems: "center", 
      justifyContent: "center", 
      backgroundColor: "#fdfdfd",
      flexDirection: "column"
    }}>
      <h1 style={{ 
        fontSize: "3rem", 
        fontWeight: "700", 
        color: "rgb(71, 80, 126)", 
        marginBottom: "1rem" 
      }}>
        Your Personal Assistant
      </h1>
      <p style={{ 
        fontSize: "1.2rem", 
        color: "rgb(71, 80, 126)", 
        marginBottom: "2rem" 
      }}>
        One-stop solution for your services. Order any service, anytime.
      </p>
      <button style={{
        padding: "0.8rem 2rem",
        fontSize: "1rem",
        fontWeight: "600",
        borderRadius: "999px",
        border: "none",
        color: "white",
        background: "linear-gradient(90deg, rgb(99, 109, 160), rgb(70, 80, 130))",
        cursor: "pointer"
      }}>
        Join Us
      </button>
    </div>
  );
};

export default LandingPage;
