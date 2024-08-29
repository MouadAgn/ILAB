import Header from "./Header";
import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { Link } from "react-router-dom";


function Treatments() {
    const [data, setData] = useState([]);
    const { id } = useParams();

    useEffect(() => {
        const fetchTreatments = async () => {
            try {
                const response = await fetch(`http://127.0.0.1:8000/api/treatments/patient/${id}`);
                const data = await response.json();
                setData(data), console.log(data);

            } catch (error) {
                console.error('Erreur lors de la récupération des données', error);
            }
        };

        fetchTreatments();
        }, [id]);

        if (!data || !data.patient || !data.treatments) {
            return <div>Loading...</div>;
        }

    const DeleteTreatment = async (patientid, treatmentid) => {
        try {
            const response = await fetch(`http://127.0.0.1:8000/api/treatments/delete/${treatmentid}/patient/${patientid}`, {
            method: 'DELETE'
            });

            if (response.ok) {
                console.log('Traitement supprimé');
                const newData = data.treatments.filter(treatment => treatment.id !== treatmentid);
                setData({ ...data, treatments: newData });
            } else {
                console.error('Erreur lors de la suppression du traitement:', await response.json());
            }
        } catch (error) {
            console.error('Erreur lors de la suppression du traitement', error);
        }
    };

    // const UpdateTreatment = async (patientid, treatmentid) => {
    //     try {
    //         const response = await fetch(`http://127.0.0.1:8000/api/treatments/update/${treatmentid}/patient/${patientid}`, {
    //         method: 'PUT'
    //         });

    //         if (response.ok) {
    //             console.log('Traitement modifié');
    //             const newData = data.treatments.filter(treatment => treatment.id !== treatmentid);
    //             setData({ ...data, treatments: newData });
    //         } else {
    //             console.error('Erreur lors de la modification du traitement:', await response.json());
    //         }
    //     } catch (error) {
    //         console.error('Erreur lors de la modification du traitement', error);
    //     }
    // }
    return (
        <div>
            <div>
            <Header />
            <h2>Informations du patient</h2>
            <p>Nom : {data.patient.name}</p>
            <table>
                <caption>Liste des traitements</caption>
                <thead>
                    <tr>
                        <th>Nom du traitement</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <th>Dosage</th>
                        <th>Commentaire</th>
                        <th>Modifier le traitement</th>
                        <th>Supprimer le traitement</th>
                    </tr>
                </thead>
                <tbody>
                    {data && data.treatments.map(treatment => (
                        <tr key={treatment.id}>
                            <td>{treatment.name}</td>
                            <td>{treatment.datestart}</td>
                            <td>{treatment.dateend}</td>
                            <td>{treatment.dosage}</td>
                            <td>{treatment.comment}</td>
                            <td><button><Link to={`/treatments/update/${treatment.id}/patient/${data.patient.id}`}>Modifier</Link></button></td>
                            <td><button onClick={() => DeleteTreatment(data.patient.id, treatment.id)}>Supprimer</button></td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
        <div>
            <button><Link to={`/treatments/add/patient/${data.patient.id}`}> Cliquer Ici pour ajouter un traitement </Link></button>
            <button><Link to={`/patients`}> Revenir à la liste des Patient </Link></button>
        </div>
        </div>
    )
}

export default Treatments;