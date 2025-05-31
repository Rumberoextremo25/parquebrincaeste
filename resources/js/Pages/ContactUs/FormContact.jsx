import Button from "@/Components/Button";
import FormGrid from "@/Components/Form/FormGrid";
import InputLabelError from "@/Components/Form/InputLabelError";
import TextAreaLabelError from "@/Components/Form/TextAreaLabelError";
import TitleSection from "@/Components/TitleSection";
import { PaperAirplaneIcon } from "@heroicons/react/24/solid";
import { useForm } from "@inertiajs/react";
import React from "react";

const FormContact = ({ form }) => {
    const { setData, data, post, processing, errors } = useForm({
        name: "",
        phone: "",
        email: "",
        subject: "",
        message: "",
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("contact_us.save"), {
            onSuccess: () => {
                // Aquí puedes mostrar un mensaje de éxito, por ejemplo:
                alert("Mensaje enviado con éxito.");
            },
            onError: () => {
                // Maneja errores si es necesario
            },
        });
    };

    const handleChange = (e) => {
        e.preventDefault();
        let target = e.target;
        setData(target.name, target.value);
    };

    return (
        <div className="mx-auto max-w-3xl">
            <TitleSection
                className="text-center"
                title="Enviar mensaje"
                subTitle="¿TIENE PREGUNTAS?"
            />
            <form onSubmit={handleSubmit}>
                <FormGrid>
                    <div className="sm:col-span-3">
                        <InputLabelError
                            handleChange={handleChange}
                            errors={errors.name} // Corrige el acceso a errores
                            label="Nombre"
                            name="name"
                            value={data.name}
                            placeholder="Tomas Rincon"
                        />
                    </div>
                    <div className="sm:col-span-3">
                        <InputLabelError
                            handleChange={handleChange}
                            errors={errors.phone} // Corrige el acceso a errores
                            label="Teléfono"
                            name="phone"
                            value={data.phone}
                            placeholder="+58-4162546978"
                        />
                    </div>
                    <div className="sm:col-span-3">
                        <InputLabelError
                            type="email"
                            handleChange={handleChange}
                            errors={errors.email} // Corrige el acceso a errores
                            label="Email"
                            name="email"
                            value={data.email}
                            placeholder="user@user.com"
                        />
                    </div>
                    <div className="sm:col-span-3">
                        <InputLabelError
                            handleChange={handleChange}
                            errors={errors.subject} // Corrige el acceso a errores
                            label="Asunto"
                            name="subject"
                            value={data.subject}
                            placeholder="Asunto del mensaje"
                        />
                    </div>
                    <div className="sm:col-span-6">
                        <TextAreaLabelError
                            rows="6"
                            handleChange={handleChange}
                            errors={errors.message} // Corrige el acceso a errores
                            label="Mensaje"
                            name="message"
                            value={data.message}
                            placeholder="Información a consultar"
                        />
                    </div>
                    <div className="sm:col-span-6 text-center">
                        <Button
                            Icon={PaperAirplaneIcon}
                            processing={processing}
                        >
                            Enviar mensaje
                        </Button>
                    </div>
                </FormGrid>
            </form>
        </div>
    );
};

export default FormContact;
