import React, { useState } from "react"; // Asegúrate de importar useState
import Button from "@/Components/Button";
import Input from "@/Components/Input";
import Label from "@/Components/Label";
import ValidationErrors from "@/Components/ValidationErrors"; // Asegúrate de que esto sea el path correcto
import { useForm } from "@inertiajs/react";
import MyAccount from "./MyAccount"; // Asumo que este es tu componente MyAccount

const ChangePassword = () => {
    // Estados para controlar la visibilidad de las contraseñas
    const [showCurrentPassword, setShowCurrentPassword] = useState(false);
    const [showNewPassword, setShowNewPassword] = useState(false);
    const [showConfirmNewPassword, setShowConfirmNewPassword] = useState(false);

    const { data, setData, processing, post, errors, reset } = useForm({
        current_password: "",
        password: "",
        password_confirmation: "",
    });

    const handleSubmit = async (e) => {
        e.preventDefault(); // Previene el comportamiento predeterminado del formulario

        // Realiza la solicitud POST a la ruta definida
        post(route("profile.store_change_password"), {
            // Nota: Con useForm, `data` ya es el payload.
            // No necesitas `data: {...}` si el nombre de las propiedades coincide
            // con las claves que esperas en el controlador.
            // Si tu controlador espera estos campos directamente, esta forma es correcta:
            // current_password: data.current_password,
            // password: data.password,
            // password_confirmation: data.password_confirmation,
            preserveScroll: true, // Preserva el desplazamiento de la página
            onSuccess: () => {
                // Reinicia los campos del formulario después de una respuesta exitosa
                reset("current_password", "password", "password_confirmation");
                // Aquí podrías añadir una notificación de éxito usando SweetAlert2 si lo usas
                // Swal.fire('¡Éxito!', 'Tu contraseña ha sido actualizada.', 'success');
            },
            onError: (errors) => {
                // Manejo de errores si es necesario
                console.error("Error al cambiar contraseña:", errors);
                // Aquí podrías añadir una notificación de error usando SweetAlert2
                // Swal.fire('Error', 'Hubo un problema al cambiar tu contraseña.', 'error');
            },
        });
    };

    const onHandleChange = (event) => {
        setData(event.target.name, event.target.value);
    };

    // Funciones para alternar la visibilidad de cada campo
    const toggleCurrentPasswordVisibility = () => {
        setShowCurrentPassword(!showCurrentPassword);
    };

    const toggleNewPasswordVisibility = () => {
        setShowNewPassword(!showNewPassword);
    };

    const toggleConfirmNewPasswordVisibility = () => {
        setShowConfirmNewPassword(!showConfirmNewPassword);
    };


    // Iconos SVG para el ojo abierto y cerrado
    const EyeOpenIcon = (props) => (
        <svg className="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
        </svg>
    );

    const EyeClosedIcon = (props) => (
        <svg className="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7a10.05 10.05 0 011.875.175M12 17a3 3 0 100-6 3 3 0 000 6z"></path>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            <line x1="4" y1="4" x2="20" y2="20" stroke="currentColor" strokeWidth="2" strokeLinecap="round"></line> {/* Línea de "tachado" */}
        </svg>
    );

    return (
        <MyAccount active="password" title="Cambiar contraseña">
            <ValidationErrors errors={errors} /> {/* Muestra errores de validación de Laravel */}

            <form onSubmit={handleSubmit}>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {/* Campo Contraseña Actual */}
                    <div className="md:col-span-2 relative">
                        <Label
                            forInput="current_password"
                            value="Contraseña Actual *"
                        />
                        <Input
                            className="w-full mt-1 pr-10" // Añadir padding derecho para el icono
                            required={true}
                            type={showCurrentPassword ? "text" : "password"} // Tipo dinámico
                            handleChange={onHandleChange}
                            value={data.current_password}
                            name="current_password"
                        />
                        <div
                            className="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer"
                            style={{ top: '1.75rem' }} // Ajusta la posición del icono si es necesario
                            onClick={toggleCurrentPasswordVisibility}
                        >
                            {showCurrentPassword ? <EyeClosedIcon /> : <EyeOpenIcon />}
                        </div>
                    </div>

                    {/* Campo Contraseña Nueva */}
                    <div className="relative">
                        <Label forInput="password" value="Contraseña nueva *" />
                        <Input
                            className="w-full mt-1 pr-10" // Añadir padding derecho para el icono
                            required={true}
                            type={showNewPassword ? "text" : "password"} // Tipo dinámico
                            handleChange={onHandleChange}
                            value={data.password}
                            name="password"
                        />
                        <div
                            className="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer"
                            style={{ top: '1.75rem' }} // Ajusta la posición del icono si es necesario
                            onClick={toggleNewPasswordVisibility}
                        >
                            {showNewPassword ? <EyeClosedIcon /> : <EyeOpenIcon />}
                        </div>
                    </div>

                    {/* Campo Confirmar Contraseña Nueva */}
                    <div className="relative">
                        <Label
                            forInput="password_confirmation"
                            value="Confirmar contraseña nueva *"
                        />
                        <Input
                            className="w-full mt-1 pr-10" // Añadir padding derecho para el icono
                            required={true}
                            type={showConfirmNewPassword ? "text" : "password"} // Tipo dinámico
                            handleChange={onHandleChange}
                            value={data.password_confirmation}
                            name="password_confirmation"
                        />
                        <div
                            className="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer"
                            style={{ top: '1.75rem' }} // Ajusta la posición del icono si es necesario
                            onClick={toggleConfirmNewPasswordVisibility}
                        >
                            {showConfirmNewPassword ? <EyeClosedIcon /> : <EyeOpenIcon />}
                        </div>
                    </div>
                </div>
                <Button className="mt-6" processing={processing}>Guardar</Button>
            </form>
        </MyAccount>
    );
};

export default ChangePassword;
