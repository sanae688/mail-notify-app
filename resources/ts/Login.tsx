import React, { useEffect } from 'react';
import axios from 'axios';

const Login: React.FC = () => {

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get('/api/loginController');

                if (response.data.success) {
                    window.location.href = response.data.redirect_url;
                } else {
                    console.error('ログイン認証エラー：', response.data.message);
                    alert('ログイン認証エラー');
                }

            } catch (error) {
                console.error('ログイン認証エラー：', error);
                alert('ログイン認証エラー');
            }
        };

        fetchData();
    }, []);

    return <div>Loading...</div>;
};

export default Login;
