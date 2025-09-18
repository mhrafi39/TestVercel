import React from "react";

function App() {
  return (
    <div style={{ padding: "20px", textAlign: "center" }}>
      <h1>React App is Working!</h1>
      <p>This is a simple React component.</p>
      <p>Current time: {new Date().toLocaleString()}</p>
    </div>
  );
}

export default App;
