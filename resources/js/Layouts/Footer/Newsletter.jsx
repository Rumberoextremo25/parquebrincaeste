import React, { useState } from "react";
import Button from "@/Components/Button";
import { useForm } from '@inertiajs/react';

const Newsletter = () => {
    // Ya no necesitas `email` como un estado separado, useForm lo manejará.
    // const [email, setEmail] = useState("");
    // `loading` es manejado por `processing` de useForm, así que ya no es necesario.
    // const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState("");
    const [error, setError] = useState(false);

    // Inicializa el hook useForm
    const {
        data,          // Objeto de datos del formulario (ej: { email: "" })
        setData,       // Función para actualizar los datos
        post,          // Método para enviar el formulario por POST
        processing,    // Estado booleano: true mientras la petición está en curso
        errors,        // Objeto de errores de validación de Laravel
        reset,         // Función para restablecer el formulario
    } = useForm({
        email: "", // Define los campos iniciales de tu formulario aquí
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        setMessage(""); // Limpia mensajes anteriores
        setError(false);

        post(route('web.newsletter'), {
            onSuccess: () => {
                setMessage("¡Gracias por suscribirte! Revisa tu bandeja de entrada.");
                setError(false);
                reset('email'); // Limpia solo el campo de email después de un envío exitoso
            },
            onError: (formErrors) => {
                // Comprueba si hay un error de validación específico para el email
                if (formErrors.email) {
                    setMessage(formErrors.email);
                } else {
                    setMessage("Disculpa, no pudimos suscribirte. Inténtalo más tarde.");
                }
                setError(true);
                console.error("Error al suscribirse:", formErrors);
            },
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
                        value={data.email} // Usa data.email del useForm hook
                        onChange={(e) => setData("email", e.target.value)} // Actualiza data.email
                        className="w-full grow border-none bg-inherit placeholder:text-gray-300 focus:ring-0"
                        placeholder="Email"
                        required
                    />
                    <button
                        className="rounded-full bg-violet-600 px-5 text-white font-semibold hover:bg-violet-700 transition duration-300 ease-in-out"
                        type="submit"
                        disabled={processing} // Usa processing de useForm
                    >
                        {processing ? 'Cargando...' : 'Suscribirse'}
                    </button>
                </form>
                {/* Muestra los errores de validación de Laravel para el campo email */}
                {errors.email && <p className="mt-2 text-sm text-red-500">{errors.email}</p>}

                {/* Muestra los mensajes de éxito o error generales */}
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