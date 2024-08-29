// Profil.jsx

import { useEffect, useState } from 'react';
import Header from './Header.jsx';
import './Profil.css';

function Profil() {
    const [data, setUserData] = useState(null);
    const storedId = localStorage.getItem('id');
    const initialId = storedId ? parseInt(storedId) : null;

    useEffect(() => {
        const fetchUserData = async () => {
            if (!initialId) {
                console.error('ID utilisateur manquant dans le stockage local');
                return;
            }
            try {
                const response = await fetch(`http://127.0.0.1:8000/api/users/${initialId}`);
                const data = await response.json();
                setUserData(data);
            } catch (error) {
                console.error('Erreur lors de la récupération des données', error);
            }
        };

        fetchUserData();
    }, [initialId]); 

    return (
        <div>
            <Header />
            <div className="profil-container">
                <h2 className="profil-title">Profil Utilisateur</h2><br></br>
                {data ? (
                    <div>
                        <p className="profil-info"><b>Nom : </b>{data.patient.Nom}</p>
                        <p className="profil-info"><b>Email : </b> {data.patient.Email}</p>
                        
                        <br></br>
                        <button className="modifier-button">Modifier</button>
                    </div>
                ) : (
                    <div className="loading-message">Chargement...</div>
                )}
            </div>
        </div>
    );
}

export default Profil;
