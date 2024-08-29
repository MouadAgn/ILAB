import { useEffect, useState } from 'react';
import './Patient.css'; // Assurez-vous d'avoir le bon fichier CSS pour ce composant
import { Link } from 'react-router-dom';
import Header from './Header.jsx';
import Footer from './Footer.jsx';

function Patient() {
    const [loading, setLoading] = useState(true); // Ajout de l'état pour le chargement
    const [appointments, setAppointments] = useState([]);
    const [searchTerm, setSearchTerm] = useState('');

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await fetch('http://127.0.0.1:8000/api/patients');
                const patientsData = await response.json();
                
                const allAppointments = [];

                for (const patient of patientsData) {
                    const patientId = patient.id;
                    const appointmentResponse = await fetch(`http://127.0.0.1:8000/api/appointments/patient/${patientId}`);
                    const appointmentsData = await appointmentResponse.json();

                    allAppointments.push({ patient: patient, appointments: appointmentsData });
                }
                
                setAppointments(allAppointments);
                setLoading(false); // Mettre à jour l'état du chargement une fois les données chargées
            } catch (error) {
                console.error('Erreur lors de la récupération des données', error);
            }
        };

        fetchData();
    }, []);

    const filteredAppointments = appointments.filter(patient => {
        return patient.appointments.patient.name.toLowerCase().includes(searchTerm.toLowerCase());
    });

    return (
        <div className="patient-container">
            <Header />
            {loading ? ( // Condition pour afficher l'icône de chargement
                <div className="loading-container">
                    <div className="loader"></div>
                </div>
            ) : (
                <>
                <br /><br />
                <h1>Liste des patients</h1>
                <input className="search-bar"
                    type="text"
                    placeholder="Rechercher par nom"
                    onChange={(e) => setSearchTerm(e.target.value)}
                    value={searchTerm}
                />
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Date de Naissance</th>
                            <th>Genre</th>
                            <th>Téléphone</th>
                            <th>Prochain Rendez-Vous</th>
                            <th>Actions</th> {/* Ajout de la colonne pour les actions */}
                        </tr>
                    </thead>
                    <tbody>
                        {filteredAppointments.map(patient => (
                            <tr key={patient.appointments.patient.id}>
                                <td>{patient.appointments.patient.id}</td>
                                <td>{patient.appointments.patient.name}</td>
                                <td>{patient.appointments.patient.dob}</td>
                                <td>{patient.appointments.patient.gender === 1 ? 'Homme' : 'Femme'}</td>
                                <td>{patient.appointments.patient.phone}</td>
                                <td>{patient.appointments.appointments.date ? `${patient.appointments.appointments.date} à ${patient.appointments.appointments.time}` : 'Pas de RDV'}</td>
                                <td>
                                    <button style={{backgroundColor: 'blue'}}><Link to={`/treatments/patient/${patient.appointments.patient.id}`}>Traitements</Link></button>&nbsp;&nbsp;
                                    <button style={{backgroundColor: 'red'}}><Link to={`/appointments/patient/${patient.appointments.patient.id}`}>Rendez-vous</Link></button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                {/* Les boutons doivent être situés en dehors de la balise <table> */}
                <Link to="/patients/add" className="add-patient-button">Ajouter un patient</Link>&nbsp;
                <Link to="/planning" className="patient-link">Retour au planning</Link>
                </>
            )}
            <Footer />
        </div>
    );
}

export default Patient;
