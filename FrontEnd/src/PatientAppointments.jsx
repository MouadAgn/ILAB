import { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import Header from "./Header";
import Footer from "./Footer";
import './PatientAppointment.css';

function PatientAppointments() {
    const [data, setData] = useState([]);
    const { id } = useParams();
    
    useEffect(() => {
        const fetchPatientData = async () => {
            try {
                const response = await fetch(`http://127.0.0.1:8000/api/appointments/patient/${id}`);
                const data = await response.json();
                setData(data);
                console.log(data);
            }
            catch (error) {
                console.error('Erreur lors de la récupération des données', error);
            }
        };
        fetchPatientData();
    }, [id]);

    if (!data || !data.patient || !data.appointments) 
    {
        return (
            <div className="loading-container">
              <div className="loader"></div>
            </div>
          );
      }
      else{
        return (
            <div className="patient-container">
                
                <Header />
                <br></br><br></br><br></br>
                <h2 className="tit">Informations du patient</h2><br></br>
                <p className="patient-info"><b>Nom : </b>  {data.patient.name}</p>
                <table className="appointments-table">
                    
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        {/* {data && data.appointments.map(appointment => ( */}
                        <tr key={data.appointments.id}>
                            <td>{data.appointments.date}</td>
                            <td>{data.appointments.time}</td>
                            <td>{data.appointments.comment}</td>
                        </tr>
                        {/* ))} */}
                    </tbody>
                </table>
                <button><Link to="/planning " className="back-link">Retour au planning</Link></button>&nbsp;
                <button><Link to="/patients" className="back-link">Retour au patients</Link></button>
                <Footer />
            </div>
        );

      }
}

export default PatientAppointments;
