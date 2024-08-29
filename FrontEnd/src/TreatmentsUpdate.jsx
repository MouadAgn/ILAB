import Header from "./Header";
import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { Link } from "react-router-dom";

function Treatments() {
    let { treatmentid, patientid } = useParams();

    // const navigate = useNavigate();
    const [data, setData] = useState([]);
    const [name, setName] = useState('');
    const [datestart, setDateStart] = useState('');
    const [dateend, setDateEnd] = useState('');
    const [dosage, setDosage] = useState('');
    const [comment, setComment] = useState('');

    useEffect(() => {
        const fetchTreatments = async () => {
            try {
                const response = await fetch(`http://127.0.0.1:8000/api/treatments/${treatmentid}`);
                const data = await response.json();
                setData(data), console.log(data);
                if (data) {
                    setName(data.name);
                    // const formattedDateStart = new Date(data.datestart).toLocaleDateString('fr-FR');
                    // const formattedDateEnd = new Date(data.dateend).toLocaleDateString('fr-FR');
                    setDateStart(data.datestart);
                    setDateEnd(data.dateend);
                    setDosage(data.dosage);
                    setComment(data.comment);
                //    console.log(data.dateEnd)
                }
            } catch (error) {
                console.error('Erreur lors de la récupération des données', error);
            }
        };

        fetchTreatments();
        }, [treatmentid]);

        if (!data) {
            return <div>Loading...</div>;
        }
    
    const handleSubmit = async (e) => {
        e.preventDefault();
            try {
                const response = await fetch(`http://127.0.0.1:8000/api/treatments/update/${treatmentid}/patient/${patientid}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name,
                    datestart,
                    dateend,
                    dosage,
                    comment
                })
                });

                if (response.ok) {
                    console.log('Traitement modifié');
                } else {
                    console.error('Erreur lors de la modification du traitement:', await response.json());
                }
            } catch (error) {
                console.error('Erreur lors de la modification du traitement', error);
            }
        }
    return (
        <div>
            <div>
                <Header />
                <h2>Information sur le traitement</h2>
                <form onSubmit={handleSubmit}>
                    <div>
                        <label htmlFor="name">Nom du traitement :</label>
                        <input
                        type="text"
                        id="name"
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        required
                        />
                    </div>
                    <div>
                        <label htmlFor="dateStart">Date de début :</label>
                        <input
                        type="date"
                        id="dateStart"
                        value={datestart}
                        onChange={(e) => setDateStart(e.target.value)}
                        required
                        />
                    </div>
                    <div>
                        <label htmlFor="dateEnd">Date de fin :</label>
                        <input
                        type="date"
                        id="dateEnd"
                        value={dateend}
                        onChange={(e) => setDateEnd(e.target.value)}
                        required
                        />
                    </div>
                    <div>
                        <label htmlFor="dosage">Dosage :</label>
                        <input
                        type="text"
                        id="dosage"
                        value={dosage}
                        onChange={(e) => setDosage(e.target.value)}
                        required
                        />
                    </div>
                    <div>
                        <label htmlFor="comment">Commentaire :</label>
                        <textarea
                        id="comment"
                        value={comment}
                        onChange={(e) => setComment(e.target.value)}
                        required
                        ></textarea>
                    </div>
                    <button type="submit">Modifier le traitement</button>
                </form>
            </div>
        <div>
            <button><Link to={`/treatments/patient/${patientid}`}> Retour à la liste des traitements </Link></button>
        </div>
        </div>
    )
}

export default Treatments;