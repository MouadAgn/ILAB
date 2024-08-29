import { useState } from 'react';
import './AddPatient.css'; // Assurez-vous d'avoir le bon fichier CSS pour ce composant
import Header from './Header';
import Footer from './Footer';

function AddPatient() {
    const [formData, setFormData] = useState({
        name: '',
        dob: '',
        gender: '',
        phone: ''
    });
    const [loading, setLoading] = useState(false); // Ajout de l'état pour le chargement
    const [message, setMessage] = useState('');


    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault(); 
        setLoading(true); // Définir le chargement sur true lors de la soumission du formulaire
        try {
            const response = await fetch('http://127.0.0.1:8000/api/patients/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            });
            if (response.ok) {
                const data = await response.json();
                console.log(data);
                setMessage('Patient ajouté avec succès !'); // Afficher un message de succès
                // Vous pouvez également rediriger l'utilisateur vers une autre page ici
            } else {
                console.error('Erreur lors de l\'ajout du patient:', response.status);
                setMessage('Erreur lors de l\'ajout du patient'); // Afficher un message d'erreur
            }
        } catch (error) {
            console.error('Erreur lors de l\'envoi de la requête:', error);
            setMessage('Erreur lors de l\'envoi de la requête'); // Afficher un message d'erreur
        }
        setLoading(false); // Définir le chargement sur false après avoir traité la requête
    };

    return (
        <div>
            <Header />
            <div className="centered-container">
                <div className="add-patient-container">
                    <h2>Ajouter un patient</h2>
                    <form onSubmit={handleSubmit}>
                        <label>Nom :</label>
                        <input type="text" name="name" value={formData.name} onChange={handleChange} />

                        <label>Date de Naissance :</label>
                        <input type="date" name="dob" value={formData.dob} onChange={handleChange} />

                        <label>Genre :</label>
                        <select name="gender" value={formData.gender} onChange={handleChange}>
                            <option value={1}>Homme</option>
                            <option value={0}>Femme</option>
                        </select>

                        <label>Téléphone :</label>
                        <input type="text" name="phone" value={formData.phone} onChange={handleChange} />

                        <button type="submit" disabled={loading}>{loading ? 'Chargement...' : 'Ajouter'}</button>
                    </form>
                    {message && <p style={{ color: message.startsWith('Erreur') ? 'red' : 'green' }}>{message}</p>}
                </div>
            </div>
            
            <Footer />
        </div>
    );
}

export default AddPatient;
