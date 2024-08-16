import React from 'react';
import './Home.css';

const Home = () => {
  return (
    <div className="home">
      <header className="header">
      <img src="/src/assets/Ilab_logo_black.png" alt="Logo ILAB" className="logo" />        <nav>
          <ul>
            <li><a href="#about">À propos</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </nav>
      </header>

      <main className="main-content">
        <section className="content-section">
          <div className="hero">
            <div className="hero-content">
              <h1>Bienvenue sur ILAB</h1>
              <p>Révolutionner la gestion des tests médicaux pour les cliniques</p>
              <a href="#contact" className="cta-button">Commencer</a>
            </div>
            <div className="hero-image" style={{backgroundImage: "url('https://images.unsplash.com/photo-1581594693702-fbdc51b2763b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1770&q=80')"}}></div>
          </div>

          <div className="info-block">
            <h2>À propos d'ILAB</h2>
            <p>ILAB est une plateforme numérique avancée conçue pour simplifier la gestion des résultats de tests diagnostiques dans les cliniques modernes. Notre solution permet de centraliser les données, facilite leur accès et leur interprétation, et aide les praticiens à identifier rapidement les éléments pertinents.</p>
          </div>

          <div className="info-block">
            <h2>Le défi</h2>
            <p>Les établissements de santé sont submergés par une masse de données provenant de divers examens. Cette abondance d'informations pose des difficultés en termes d'organisation, de traitement et augmente les risques d'erreurs ou d'oublis.</p>
          </div>

          <div className="info-block">
            <h2>Notre solution</h2>
            <p>ILAB vise à optimiser le temps et minimiser les risques d'erreurs. Notre plateforme allège la charge administrative liée à la gestion des tests, permettant aux professionnels de santé de se recentrer sur leur mission principale : les soins aux patients.</p>
          </div>

          <div className="image-container">
            <img src="https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1632&q=80" alt="Analyse médicale" />
          </div>

          <div className="info-block">
            <h2>Fonctionnalités clés</h2>
            <ul>
              <li>Centralisation des données de tests</li>
              <li>Notifications en temps réel des résultats</li>
              <li>Gestion sécurisée des données des patients</li>
              <li>Interface utilisateur intuitive pour les professionnels de santé</li>
            </ul>
          </div>
        </section>
      </main>

      <footer className="footer">
        <p>&copy; 2024 ILAB. Tous droits réservés.</p>
      </footer>
    </div>
  );
};

export default Home;