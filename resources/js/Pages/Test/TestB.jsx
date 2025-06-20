// resources/js/Pages/PageB.jsx

import React from 'react';
import { Head, Link,usePage  } from '@inertiajs/react';

// El componente PageB ahora recibe 'props'.
// Si Laravel le pasa 'receivedMessage', este estará disponible aquí.
export default function PageB() {

     const { flash } = usePage().props; 
    
    return (
        <div style={{ padding: '20px', fontFamily: 'Arial, sans-serif' }}>
            <Head title="Página B (Mensaje Recibido)" />

            <h1>Estás en la Página B</h1>
            <p>Desde aquí puedes volver a la Página A.</p>

            {/* Mostramos el mensaje recibido si existe */}
            {flash.message && (
                <div style={{
                    marginTop: '30px',
                    padding: '15px',
                    backgroundColor: '#e6ffe6',
                    border: '1px solid #aaffaa',
                    borderRadius: '5px',
                    color: '#006600'
                }}>
                    <strong>Mensaje recibido de Página A:</strong> { flash.message}
                </div>
            )}

            <Link href="/test-a" style={{
                display: 'inline-block',
                marginTop: '30px',
                padding: '10px 20px',
                backgroundColor: '#28a745',
                color: 'white',
                textDecoration: 'none',
                borderRadius: '5px'
            }}>
                Volver a la Página A
            </Link>
        </div>
    );
}