async function login(email, password) {
  const res = await fetch('/api/login', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({email, password})
  });
  const data = await res.json();
  if (data.token) {
    localStorage.setItem('api_token', data.token);
    console.log('Login successful');
  } else {
    console.error('Login failed', data);
  }
}

async function getEvents() {
  const token = localStorage.getItem('api_token');
  const res = await fetch('/api/events', {
    headers: { 'Authorization': 'Bearer ' + token }
  });
  const data = await res.json();
  console.log(data.events);
}

// Usage example:
// login('user@example.com', 'password123');
// getEvents();
