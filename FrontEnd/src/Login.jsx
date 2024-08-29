import { useState } from 'react';
import Header from './Header.jsx';
import './Login.css';


export default function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [errorMessage, setErrorMessage] = useState('');
  
  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      // console.log("Données envoyées :", { email, password });
      const response = await fetch('http://127.0.0.1:8000/api/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password }),
        credentials: 'same-origin'
      });
      console.log("Réponse de l'API :", response);
      const data = await response.json();
      console.log(data);
      if (response.ok) {
        localStorage.setItem('id', JSON.stringify(data.id));
        // Redirigez l'utilisateur vers une autre page (par exemple, page d'accueil)
        window.location.href = '/planning'; // Remplacez '/home' par l'URL de la page souhaitée
      } else {
        console.log("testr");
        setErrorMessage(data.message);
      }
    } catch (error) {
      console.error('Erreur lors de la connexion', error);
    }
  };

  return (
    <div>
      <Header />
      <div className="login-container">
        <form className="login-form" onSubmit={handleSubmit}>
          <h2>Connexion</h2><br></br>
          {errorMessage && <p className="error-message">{errorMessage}</p>}
          <div className="input-group">
            <label htmlFor="email">Adresse e-mail :</label>
            <input
              type="Email"
              id="Email"
              name="Email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
          </div>
          <div className="input-group">
            <label htmlFor="password">Mot de passe :</label>
            <input
              type="Password"
              id="Password"
              name="Password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
          </div>
          <button type="submit">Se connecter</button>
        </form>
      </div>
    </div>
  );
}
