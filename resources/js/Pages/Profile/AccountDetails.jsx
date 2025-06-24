import Button from "@/Components/Button";
import Input from "@/Components/Input";
import Label from "@/Components/Label";
import ValidationErrors from "@/Components/ValidationErrors"; // Lo usas en MyAccount, pero es bueno tenerlo en mente
import { useForm } from "@inertiajs/react";
import MyAccount from "./MyAccount";

const AccountDetails = ({ auth, errors: pageErrors }) => { // Renombra 'errors' de la página para evitar conflictos
    // Inicializa 'phone' con un string vacío si es null/undefined para asegurar que el input sea controlado
    const { data, setData, processing, post, errors } = useForm({
        name: auth.user.name || '',
        phone: auth.user.phone || '',
        email: auth.user.email || '',
        email_confirmation: auth.user.email || '',
    });

    const handleSubmit = async (e) => {
        e.preventDefault();
        // Puedes agregar un console.log aquí para verificar que se ejecuta al presionar el botón
        console.log('Enviando formulario de detalles de cuenta. Datos:', data);
        post(route("store_account_details"), { preserveScroll: true });
    };

    const onHandleChange = (event) => {
        setData(event.target.name, event.target.value);
    };

    return (
        <MyAccount active="account-details" title="Detalles de Cuenta">
            {/* ValidationErrors se muestra en MyAccount, pero es bueno recordarlo */}
            {/* <ValidationErrors errors={errors} /> // Los errores específicos del formulario se pueden mostrar aquí también */}

            <form onSubmit={handleSubmit}>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8 ">
                    <div>
                        {/* Mejora el 'forInput' para que coincida con el 'id' del Input */}
                        <Label forInput="name" value="Nombre y Apellido *" />
                        <Input
                            id="name" // Añade el ID para accesibilidad
                            className="w-full mt-1"
                            required={true}
                            handleChange={onHandleChange}
                            name="name"
                            value={data.name}
                        />
                        {/* Muestra errores de validación específicos para este campo */}
                        {errors.name && <div className="text-red-500 text-sm mt-1">{errors.name}</div>}
                    </div>

                    <div>
                        {/* Label y Input para Teléfono - Asegura la conexión con 'phone' */}
                        <Label forInput="phone" value="Teléfono *" />
                        <Input
                            id="phone" // ¡Importante! Añade el ID para que el Label funcione
                            className="w-full mt-1"
                            required={true}
                            handleChange={onHandleChange}
                            name="phone" // El 'name' coincide con la clave en 'data'
                            value={data.phone} // El 'value' se vincula al estado 'data.phone'
                            type="tel" // Usa 'tel' para teléfonos (mejora la experiencia en móviles)
                            placeholder="Ej: +58 412 1234567" // Placeholder útil
                        />
                        {/* Muestra errores de validación específicos para el teléfono */}
                        {errors.phone && <div className="text-red-500 text-sm mt-1">{errors.phone}</div>}
                    </div>

                    <div>
                        {/* Label y Input para Email */}
                        <Label forInput="email" value="Email *" />
                        <Input
                            id="email" // Añade el ID
                            className="w-full mt-1"
                            required={true}
                            type="email"
                            handleChange={onHandleChange}
                            name="email"
                            value={data.email}
                        />
                        {errors.email && <div className="text-red-500 text-sm mt-1">{errors.email}</div>}
                    </div>

                    <div>
                        {/* Label y Input para Confirmar Email */}
                        <Label forInput="email_confirmation" value="Confirmar Email *" /> {/* Cambia forInput */}
                        <Input
                            id="email_confirmation" // Añade el ID
                            className="w-full mt-1"
                            required={true}
                            type="email"
                            handleChange={onHandleChange}
                            value={data.email_confirmation}
                            name="email_confirmation"
                        />
                        {errors.email_confirmation && <div className="text-red-500 text-sm mt-1">{errors.email_confirmation}</div>}
                    </div>
                </div>

                <Button className="mt-6" processing={processing}>
                    Guardar
                </Button>
            </form>
        </MyAccount>
    );
};

export default AccountDetails;
