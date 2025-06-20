import React, { useState } from "react";  
import Button from "@/Components/Button";
import { useForm  } from '@inertiajs/react';

const Newsletter = () => {
    const [email, setEmail] = useState("");
    const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState("");
    const [error, setError] = useState(false);

    // Inicializa el hook useForm
    const {
        data,      // Objeto de datos del formulario (ej: { email: "" })
        setData,   // Función para actualizar los datos
        post,      // Método para enviar el formulario por POST
        processing, // Estado booleano: true mientras la petición está en curso
        errors,     // Objeto de errores de validación de Laravel
    } = useForm({
        email: "", // Define los campos iniciales de tu formulario aquí
    });


    // const handleSubmit = (e) => {
    //     e.preventDefault();
    //     setLoading(true);
    //     setMessage("");
    //     setError(false);

    //     Inertia.post('/web/newsletter', { email }, {
    //         onSuccess: () => {
    //             setMessage("¡Gracias por suscribirte!");
    //             setEmail("");
    //         },
    //         onError: (errors) => {
    //             setMessage("Disculpa, pero no pudimos suscribirte. Inténtalo más tarde.");
    //             setError(true);
    //             console.error("Error al suscribirse:", errors);
    //         },
    //         onFinish: () => {
    //             setLoading(false);
    //         }
    //     });
    // };

    const handleSubmit = (e) => {
        e.preventDefault();
        setMessage(""); // Limpia mensajes anteriores
        setError(false);

        // El método 'post' de useForm ya maneja 'processing' automáticamente.
        // Pasa los datos directamente como segundo argumento.
        post(route('web.newsletter'), { // Asegúrate de que 'route' esté configurado para Laravel Ziggy
            // El primer argumento son los datos a enviar. 'data' ya contiene el email.
            // No necesitas { email: email } aquí porque 'data' ya es el payload.
        }, {
            onSuccess: () => {
                setMessage("¡Gracias por suscribirte!");
                setData("email", ""); // Limpia el campo de email del formulario
                setError(false);
            },
            onError: (formErrors) => { // 'errors' ya lo proporciona useForm, así que lo renombramos a 'formErrors' para este callback
                setMessage("Disculpa, pero no pudimos suscribirte. Inténtalo más tarde.");
                setError(true);
                console.error("Error al suscribirse:", formErrors);
            },
            // 'onFinish' se encarga de restablecer 'processing' automáticamente.
        });
    };





    return (
        <div className="relative overflow-hidden rounded-3xl py-8 px-4 md:py-12 md:px-5">
            <div className="relative z-10 mx-auto max-w-2xl text-center">
                <p className="text-lg font-medium uppercase text-white md:text-2xl">
                    Suscríbete
                </p>
                <h3 className="mt-4 text-2xl font-bold uppercase md:text-4xl text-white">
                    PARA OBTENER BENEFICIOS EXCLUSIVOS
                </h3>
                <form onSubmit={handleSubmit} className="bg-white mt-12 flex h-12 w-full items-stretch rounded-full border border-white border-opacity-30 pl-5 shadow">
                    <input
                        type="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        className="w-full grow border-none bg-inherit placeholder:text-gray-300 focus:ring-0"
                        placeholder="Email"
                        required
                    />
                    <button className="rounded-full" type="submit" disabled={loading}>
                        {loading ? 'Cargando...' : 'Suscribirse'}
                    </button>
                </form>
                {message && (
                    <p className={`mt-4 text-sm ${error ? 'text-red-500' : 'text-green-500'}`}>
                        {message}
                    </p>
                )}
                <p className="mt-8 text-sm text-white">
                    Respetamos su privacidad, por lo que nunca compartiremos su información.
                </p>
            </div>
            <div className="absolute inset-0 bg-gradient-to-r from-violet-500 to-violet-600"></div>
        </div>
    );
};

export default Newsletter;
