import { useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import './TreatmentsForm.css';
import Header from './Header';
import Footer from './Footer';


const TreatmentForm = () => {
  let { id } = useParams();
  const navigate = useNavigate();
  const [name, setName] = useState('');
  const [dateStart, setDateStart] = useState('');
  const [dateEnd, setDateEnd] = useState('');
  const [dosage, setDosage] = useState('');
  const [comment, setComment] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      const response = await fetch(`http://127.0.0.1:8000/api/treatments/add/patient/${id}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          id,
          name,
          dateStart,
          dateEnd,
          dosage,
          comment
        })
      });

      if (response.ok) {
        navigate(`/treatments/patient/${id}`)
      } else {
        console.error('Erreur lors de l\'ajout du traitement:', await response.json());
      }
    } catch (error) {
      console.error(error);
    }
  };

  return (
    <div><Header /><br></br>
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
          value={dateStart}
          onChange={(e) => setDateStart(e.target.value)}
          required
        />
      </div>
      <div>
        <label htmlFor="dateEnd">Date de fin :</label>
        <input
          type="date"
          id="dateEnd"
          value={dateEnd}
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
      <button type="submit">Ajouter le traitement</button>
    </form>
    <Footer />
    
    
    </div>
  );
};

export default TreatmentForm;