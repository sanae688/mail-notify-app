import { createRoot } from 'react-dom/client';
import Login from './Login';

const container = document.getElementById('login');
const root = createRoot(container!);

root.render(
    <div className="Login">
        <Login />
    </div>
);
