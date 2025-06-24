import React, { useState, useEffect } from "react";
import Button from "@/Components/Button";
import Checkbox from "@/default-Components/Checkbox";
import Guest from "@/Layouts/Guest";
import Input from "@/Components/Input"; // Asumo que este es tu componente de Input
import Label from "@/Components/Label"; // Asumo que este es tu componente de Label
import { Head, Link, useForm } from "@inertiajs/react";
import InputError from "@/Components/InputError";

export default function Login({ status, canResetPassword }) {
    // Estado para controlar la visibilidad de la contraseña
    const [showPassword, setShowPassword] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        email: "correo@proveedor.com", // Puedes dejarlo vacío o con un valor por defecto para desarrollo
        password: "123456789", // Puedes dejarlo vacío para producción
        remember: false, // Por defecto a false es más seguro
    });

    useEffect(() => {
        return () => {
            reset("password");
        };
    }, []);

    const onHandleChange = (event) => {
        setData(
            event.target.name,
            event.target.type === "checkbox"
                ? event.target.checked
                : event.target.value
        );
    };

    const submit = (e) => {
        e.preventDefault();
        post(route("login"));
    };

    // Función para alternar la visibilidad de la contraseña
    const togglePasswordVisibility = () => {
        setShowPassword(!showPassword);
    };

    return (
        <Guest>
            <Head title="Iniciar sesión" />

            {status && (
                <div className="mb-4 text-sm font-medium text-blue-400">
                    {status}
                </div>
            )}

            <div className="text-center">
                <span className="text-2xl text-blue-400">BIENVENIDO</span>
            </div>
            <form onSubmit={submit} className="mt-4">
                <div>
                    <Label forInput="email" value="Correo" />

                    <Input
                        required
                        type="text"
                        name="email"
                        value={data.email}
                        placeholder={"email"}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        isFocused={true}
                        handleChange={onHandleChange}
                    />
                    <InputError message={errors.email} className="mt-2" />
                </div>

                <div className="mt-4 relative"> {/* Agregamos 'relative' para posicionar el icono */}
                    <Label forInput="password" value="Contraseña" />

                    <Input
                        required
                        // Cambiamos el tipo dinámicamente basado en 'showPassword'
                        type={showPassword ? "text" : "password"}
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full pr-10" // Añadimos pr-10 para dejar espacio al icono
                        autoComplete="current-password"
                        handleChange={onHandleChange}
                    />
                    {/* Icono de ojo para alternar visibilidad */}
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
                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="mt-6 block text-sm">
                    <div className="flex items-center justify-between">
                        <label className="flex items-center">
                            <Checkbox
                                name="remember"
                                value={data.remember}
                                onChange={onHandleChange}
                            />
                            <span className="ml-2">Acuérdate de mí</span>
                        </label>
                        {canResetPassword && (
                            <Link
                                href={route("password.request")}
                                className="underline hover:text-gray-300"
                            >
                                ¿Olvidaste tu contraseña?
                            </Link>
                        )}
                    </div>
                </div>

                <div className="mt-8 flex items-center justify-end">
                    <Button className="ml-4" processing={processing}>
                        Iniciar sesión
                    </Button>
                </div>
                <div className="mt-4 text-center text-sm">
                    ¿No tienes una cuenta?{" "}
                    <Link
                        href={route("register")}
                        className="font-bold text-blue-400 hover:underline"
                    >
                        Regístrate ahora
                    </Link>
                </div>
            </form>
        </Guest>
    );
}
