import React from 'react';
import FrequentlyAsked from "@/Components/FrequentlyAsked";
import Layout from "@/Layouts/Layout";
import BannerHero from '@/Components/Hero/BannerHero';

const packagesData = [
    {
        name: "PLAN BASICO",
        image: "/img/paquetes/planbasico.PNG",
        description: [
            "3 Horas de Disfrute.",
            "¡El Cumpleañero Tiene Entrada, Medias y Comidas Gratis!",
            "1 Cilindro",
            "1 Paraban Personalizado con el motivo de la fiesta",
            "150 Globos y una alfombra o grama de base.",
            "11 brazaletes + 11 medias antideslizantes (Para los niños).",
            "11 Comidas de cada concesión del parque."
        ],
        cost: { weekdays: "$600", weekends: "$750" },
        availability: { weekdays: "Martes a Jueves", weekends: "Viernes a Domingo" },
        contact: {
            name: "Brinca Este 2024 C.A",
            phone: "(+58) 412-3508826",
            email: "tickets@parquebrincaeste.com"
        }
    },
    {
        name: "MEDIUM",
        image: "/img/paquetes/IMG_7017.PNG",
        description: [
            "3 Horas de Disfrute.",
            "¡El Cumpleañero Tiene Entrada, Medias y Comidas Gratis!",
            "3 Cilindros",
            "1 Paraban Personalizado con el motivo de la fiesta",
            "200 Globos y una alfombra o grama de base.",
            "16 brazaletes + 16 medias antideslizantes (Para los niños).",
            "16 Comidas de cada concesión del parque."
        ],
        cost: { weekdays: "$800", weekends: "$980" },
        availability: { weekdays: "Martes a Jueves", weekends: "Viernes a Domingo" },
        contact: {
            name: "Brinca Este 2024 C.A",
            phone: "(+58) 412-3508826",
            email: "tickets@parquebrincaeste.com"
        }
    },
    {
        name: "VIP",
        image: "/img/paquetes/IMG_7019.PNG",
        description: [
            "3 Horas de Disfrute.",
            "¡El Cumpleañero Tiene Entrada, Medias y Comidas Gratis!",
            "3 Cilindros y Piso Rotulado",
            "1 Paraban Personalizado + Nombre del Cumpleañero (3m²)",
            "400 Globos (Normales y Cromados).",
            "1 Letra, Figura o Número de 85cm.",
            "26 brazaletes + 26 medias antideslizantes (Para los niños).",
            "26 comidas de cada concesión del parque."
        ],
        cost: { weekdays: "$1200", weekends: "$1500" },
        availability: { weekdays: "Martes a Jueves", weekends: "Viernes a Domingo" },
        contact: {
            name: "Brinca Este 2024 C.A",
            phone: "(+58) 412-3508826",
            email: "tickets@parquebrincaeste.com"
        }
    },
    {
        name: "Sopla Tus Velitas",
        image: "/img/paquetes/IMG_7145.PNG",
        description: [
            "2 Horas de Disfrute.",
            "¡El Cumpleañero Entra Gratis!",
            "Incluye entradas y medias para 10 amiguitos.",
            "Trae tu torta y te obsequiamos:",
            "2 Pizzas Familiares",
            "2 Refrescos"
        ],
        cost: { weekdays: "$200", weekends: "$250" },
        availability: { weekdays: "Martes a Jueves", weekends: "Viernes a Domingo" },
        contact: {
            name: "Brinca Este 2024 C.A",
            phone: "(+58) 412-3508826",
            email: "tickets@parquebrincaeste.com"
        }
    },
    {
        name: "Gratis",
        image: "/img/paquetes/IMG_4416.PNG",
        description: [
            "Trae a 5 personas que adquieran entradas y medias.",
            "¡El Cumpleañero tiene entrada y medias gratis!",
            "Traer tu torta y canta cumple con nosotros."
        ],
        cost: { weekdays: "Gratis", weekends: "" },
        availability: { weekdays: "Martes a Domingos", weekends: "" },
        contact: {
            name: "Brinca Este 2024 C.A",
            phone: "(+58) 412-3508826",
            email: "tickets@parquebrincaeste.com"
        }
    }
];

const Package = () => {
    return (
        <Layout>
            <BannerHero title="EVENTOS PRIVADOS & CUMPLEAÑOS" />
            <div className="py-section container mx-auto px-4 sm:px-6 lg:px-8">
                {/* Contenedor principal de las tarjetas */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {packagesData.map((pkg, index) => (
                        <div
                            key={index}
                            className="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col"
                        >
                            {/* Imagen del paquete */}
                            <div className="relative h-64 md:h-56 lg:h-48 overflow-hidden">
                                <img
                                    src={pkg.image}
                                    alt={pkg.name}
                                    className="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                />
                                {/* Título del paquete superpuesto */}
                                <div className="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-4 text-white">
                                    <h3 className="text-2xl font-bold">{pkg.name}</h3>
                                </div>
                            </div>

                            {/* Contenido de la tarjeta */}
                            <div className="p-6 flex-grow">
                                <div className="mb-4">
                                    <p className="font-semibold text-gray-800 mb-2">Descripción:</p>
                                    <ul className="list-disc list-inside text-sm text-gray-700 space-y-1">
                                        {pkg.description.map((line, i) => (
                                            <li key={i}>{line}</li>
                                        ))}
                                    </ul>
                                </div>

                                <div className="mb-4">
                                    <p className="font-semibold text-gray-800 mb-2">Costo:</p>
                                    <p className="font-bold text-lg text-pink-600">
                                        {pkg.cost.weekdays}
                                        {pkg.cost.weekends && ` / ${pkg.cost.weekends}`}
                                    </p>
                                </div>

                                <div className="mb-4">
                                    <p className="font-semibold text-gray-800 mb-2">Disponibilidad:</p>
                                    <p className="text-green-500 font-medium">
                                        {pkg.availability.weekdays}
                                        {pkg.availability.weekends && ` / ${pkg.availability.weekends}`}
                                    </p>
                                </div>

                                <div className="text-gray-700">
                                    <p className="font-semibold text-gray-800 mb-2">Contacto:</p>
                                    <p className="text-sm">
                                        <strong>Nombre:</strong> {pkg.contact.name}<br />
                                        <strong>Teléfono:</strong> {pkg.contact.phone}<br />
                                        <strong>Correo:</strong> <a href={`mailto:${pkg.contact.email}`} className="text-blue-600 hover:underline">
                                            {pkg.contact.email}
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
            <FrequentlyAsked />
        </Layout>
    );
};

export default Package;