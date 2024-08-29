import './Header.css';
import { Link } from 'react-router-dom';
import logo from '../src/assets/Ilab_logo_black.png'

export default function Header() {
  return (
    <div>
      <header>
        <div className="logo">
          <Link to="/">
            <img src={logo} alt="Logo ILAB" />
          </Link>
        </div>
        <nav>
          <ul>
            <li><Link to="/patients">Patients</Link></li>
            <li><Link to="/planning">Planning</Link></li>
            {/* <li><Link to="/traitements">Traitements</Link></li> */}
            <li><Link to="/login">Login</Link></li>
            <li><Link to="/profil">Profil</Link></li>
          </ul>
        </nav>
      </header>
    </div> 
  )
}