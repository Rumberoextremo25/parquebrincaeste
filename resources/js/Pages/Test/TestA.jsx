// resources/js/Pages/PageA.jsx

import React from 'react';
import { Head, useForm } from '@inertiajs/react'; // Importamos useForm

export default function TestA() {
    // Usamos useForm para manejar el estado del formulario
    const { data, setData, post, processing, errors } = useForm({
        message: '', // Campo para el mensaje que enviaremos
    });

    // Función que se ejecuta al enviar el formulario
    const handleSubmit = (e) => {
        e.preventDefault(); // Previene el comportamiento por defecto del formulario (recargar página)
        // Envía los datos del formulario a la ruta '/send-message' usando el método POST
        // Inertia intercepta esto y lo convierte en una petición AJAX
        post('/send-message');
    };

    return (
        <div style={{ padding: '20px', fontFamily: 'Arial, sans-serif' }}>
            <Head title="Página A (Enviar Mensaje)" />

            <h1>Página A: Envía un mensaje a la Página B</h1>
            <p>Escribe algo y haz clic en "Enviar Mensaje".</p>

            <form onSubmit={handleSubmit} style={{
                marginTop: '30px',
                padding: '20px',
                border: '1px solid #ccc',
                borderRadius: '8px',
                maxWidth: '400px'
            }}>
                <label htmlFor="message" style={{ display: 'block', marginBottom: '8px', fontWeight: 'bold' }}>
                    Tu Mensaje:
                </label>
                <input
                    id="message"
                    type="text"
                    value={data.message}
                    onChange={(e) => setData('message', e.target.value)} // Actualiza el estado al escribir
                    disabled={processing} // Deshabilita el input mientras se envía el formulario
                    style={{
                        width: 'calc(100% - 22px)',
                        padding: '10px',
                        marginBottom: '10px',
                        border: '1px solid #ddd',
                        borderRadius: '4px'
                    }}
                />
                {errors.message && ( // Muestra errores de validación si existen
                    <div style={{ color: 'red', fontSize: '0.9em', marginBottom: '10px' }}>
                        {errors.message}
                    </div>
                )}

                <button
                    type="submit"
                    disabled={processing} // Deshabilita el botón mientras se envía
                    style={{
                        padding: '12px 25px',
                        backgroundColor: '#007bff',
                        color: 'white',
                        border: 'none',
                        borderRadius: '5px',
                        cursor: processing ? 'not-allowed' : 'pointer'
                    }}
                >
                    {processing ? 'Enviando...' : 'Enviar Mensaje a Página B'}
                </button>
            </form>
        </div>
    );
}