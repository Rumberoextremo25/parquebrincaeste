import React, { useState, useEffect } from 'react';
import Button from '@/Components/Button';
import Guest from '@/Layouts/Guest';
import Input from '@/Components/Input';
import Label from '@/Components/Label';
import ValidationErrors from '@/Components/ValidationErrors'; // Asegúrate de que esto sea el path correcto
import { Head, Link, useForm } from '@inertiajs/react';

export default function Register() {
    // Estados para controlar la visibilidad de las contraseñas
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    useEffect(() => {
        return () => {
            reset('password', 'password_confirmation');
        };
    }, []);

    const onHandleChange = (event) => {
        setData(event.target.name, event.target.type === 'checkbox' ? event.target.checked : event.target.value);
    };

    const submit = (e) => {
        e.preventDefault();
        post(route('register'));
    };

    // Función para alternar la visibilidad de la primera contraseña
    const togglePasswordVisibility = () => {
        setShowPassword(!showPassword);
    };

    // Función para alternar la visibilidad de la confirmación de contraseña
    const toggleConfirmPasswordVisibility = () => {
        setShowConfirmPassword(!showConfirmPassword);
    };

    return (
        <Guest>
            <Head title="Register" />
            {/* ValidationErrors se muestra automáticamente si hay errores pasados desde Laravel */}
            <ValidationErrors errors={errors} />

            <form onSubmit={submit}>
                <div>
                    <Label forInput="name" value="Nombre" />
                    <Input
                        type="text"
                        name="name"
                        value={data.name}
                        className="mt-1 block w-full"
                        autoComplete="name"
                        isFocused={true}
                        handleChange={onHandleChange}
                        required
                    />
                </div>

                <div className="mt-4">
                    <Label forInput="email" value="Correo" />
                    <Input
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        handleChange={onHandleChange}
                        required
                    />
                </div>

                <div className="mt-4 relative"> {/* Agregamos 'relative' para posicionar el icono */}
                    <Label forInput="password" value="Contraseña" />
                    <Input
                        // Cambiamos el tipo dinámicamente basado en 'showPassword'
                        type={showPassword ? "text" : "password"}
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full pr-10"
                        autoComplete="new-password"
                        handleChange={onHandleChange}
                        required
                    />
                    {/* Icono de ojo para alternar visibilidad de la primera contraseña */}
                    <div
                        className="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer"
                        style={{ top: '1.75rem' }} /* Ajusta la posición del icono */
                        onClick={togglePasswordVisibility}
                    >
                        {showPassword ? (
                            // Icono de ojo abierto (para ocultar)
                            <svg className="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7a10.05 10.05 0 011.875.175M12 17a3 3 0 100-6 3 3 0 000 6z"></path>
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                <line x1="4" y1="4" x2="20" y2="20" stroke="currentColor" strokeWidth="2" strokeLinecap="round"></line> {/* Línea de "tachado" */}
                            </svg>
                        ) : (
                            // Icono de ojo cerrado (para mostrar)
                            <svg className="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        )}
                    </div>
                </div>

                <div className="mt-4 relative"> {/* Agregamos 'relative' para posicionar el icono */}
                    <Label forInput="password_confirmation" value="Confirmar Contraseña" />
                    <Input
                        // Cambiamos el tipo dinámicamente basado en 'showConfirmPassword'
                        type={showConfirmPassword ? "text" : "password"}
                        name="password_confirmation"
                        value={data.password_confirmation}
                        className="mt-1 block w-full pr-10"
                        handleChange={onHandleChange}
                        required
                    />
                    {/* Icono de ojo para alternar visibilidad de la confirmación de contraseña */}
                    <div
                        className="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer"
                        style={{ top: '1.75rem' }} /* Ajusta la posición del icono */
                        onClick={toggleConfirmPasswordVisibility}
                    >
                        {showConfirmPassword ? (
                            // Icono de ojo abierto (para ocultar)
                            <svg className="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7a10.05 10.05 0 011.875.175M12 17a3 3 0 100-6 3 3 0 000 6z"></path>
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                <line x1="4" y1="4" x2="20" y2="20" stroke="currentColor" strokeWidth="2" strokeLinecap="round"></line> {/* Línea de "tachado" */}
                            </svg>
                        ) : (
                            // Icono de ojo cerrado (para mostrar)
                            <svg className="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        )}
                    </div>
                </div>

                <div className="flex items-center justify-end mt-8">
                    <Link href={route('login')} className="underline text-sm hover:text-gray-200">
                        ¿Ya Registrado?
                    </Link>

                    <Button className="ml-4" processing={processing}>
                        Registrar
                    </Button>
                </div>
            </form>
        </Guest>
    );
}