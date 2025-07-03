import React from 'react';
import BannerHero from '@/Components/Hero/BannerHero';
import Layout from '@/Layouts/Layout';
import { Link } from '@inertiajs/react'; // Se mantiene si se usa en el futuro

// const Terminos = () => {  <-- Esta es la declaración que quieres usar
const Terminos = () => { // Nombre del componente ParkRules
    const rulesData = [ // Contenido ahora específico para "Reglas del Parque" con 12 reglas
        {
            title: "1. Calcetines Antideslizantes Obligatorios",
            content: [
                "Por su seguridad y la de todos los visitantes, el uso de nuestros **calcetines antideslizantes especiales** es estrictamente obligatorio en todas las áreas de trampolines y atracciones. No se permitirá el ingreso sin ellos."
            ]
        },
        {
            title: "2. Exoneración de Responsabilidad",
            content: [
                "Todos los participantes, o sus padres/tutores legales en caso de menores, deben firmar una **exoneración de responsabilidad** antes de ingresar. Este documento es un requisito indispensable para participar en las actividades del parque."
            ]
        },
        {
            title: "3. Respetar al Personal y las Señales",
            content: [
                "Siempre siga las **instrucciones de nuestro personal** y preste atención a todas las señales de seguridad. Ellos están aquí para asegurar una experiencia segura y divertida para todos."
            ]
        },
        {
            title: "4. Una Persona por Trampolín",
            content: [
                "Para evitar colisiones y lesiones graves, solo se permite **una persona por trampolín** a la vez. El salto doble está prohibido."
            ]
        },
        {
            title: "5. Prohibido Correr y Empujar",
            content: [
                "Está terminantemente **prohibido correr, empujar, luchar o jugar de forma brusca** en todas las áreas del parque. Mantenga siempre una distancia segura con otros saltadores."
            ]
        },
        {
            title: "6. Restricciones de Edad y Estatura",
            content: [
                "Ciertas atracciones pueden tener **restricciones de edad o estatura** por razones de seguridad. Consulte las señalizaciones específicas en cada área y respételas."
            ]
        },
        {
            title: "7. Objetos Personales",
            content: [
                "Vacía tus bolsillos y **retira todos los objetos sueltos** (joyas, cinturones con hebillas grandes, llaves, teléfonos, etc.) antes de saltar. Utiliza nuestros casilleros para guardar tus pertenencias."
            ]
        },
        {
            title: "8. Prohibido Alimentos y Bebidas",
            content: [
                "No está permitido el ingreso de **alimentos, bebidas o chicles** a las áreas de juego. Consuma solo en las zonas designadas para ello."
            ]
        },
        {
            title: "9. Conducta Responsable y Salud",
            content: [
                "Los participantes utilizan las instalaciones **bajo su propio riesgo**. No salte si tiene alguna condición médica preexistente que pueda agravarse (lesiones de espalda, problemas cardíacos, embarazo, etc.). Si se siente indispuesto, informe a nuestro personal inmediatamente.",
                "Está prohibido el ingreso al parque bajo la influencia de alcohol o sustancias ilícitas."
            ]
        },
        {
            title: "10. Reglas Específicas por Zona",
            content: [
                "**Foso de Espuma/Airbag**: Salte siempre de pies o sentado. Asegúrese de que el foso esté despejado antes de saltar. Salga del foso tan pronto como sea posible.",
                "**Canastas de Baloncesto**: Un solo saltador por carril. No se cuelgue del aro.",
                "**Muros de Escalada/Parkour**: Solo para usuarios experimentados. Siga las instrucciones específicas de los monitores en todo momento."
            ]
        },
        {
            title: "11. Seguridad de los Menores",
            content: [
                "Los **niños pequeños deben estar supervisados** por un adulto responsable en todo momento. Los adultos supervisores son responsables de asegurar que los menores a su cargo cumplan todas las normas."
            ]
        },
        {
            title: "12. Derecho de Admisión y Permanencia",
            content: [
                "Brinca Este Jumping Park se reserva el derecho de **negar la admisión o solicitar la salida** a cualquier persona que no cumpla con estas normas, que ponga en riesgo su propia seguridad o la de otros, o que muestre una conducta inapropiada, sin derecho a reembolso."
            ]
        }
    ];

    return (
        <Layout title="Normas del Parque">
            <BannerHero
                title="NORMAS DEL PARQUE"
                desc="Para la seguridad y el disfrute de todos en Brinca Este Jumping Park, le pedimos que lea y respete nuestras normas. ¡Su cumplimiento garantiza la máxima diversión!"
                //img="/img/home/park_rules_banner.jpg"
            />

            <div className="container mx-auto p-4 py-16 sm:py-24">
                <div className="bg-white p-8 sm:p-12 lg:p-16 rounded-2xl shadow-xl border border-gray-100 max-w-4xl mx-auto">
                    <h1 className="text-4xl font-extrabold text-gray-900 mb-8 text-center leading-tight">
                        Normas de Brinca Este Jumping Park
                    </h1>
                    <p className="text-gray-600 text-lg mb-10 text-center">
                        Al ingresar a nuestras instalaciones, usted acepta las siguientes normas de seguridad y uso. Su cooperación es fundamental para una experiencia divertida y segura para todos.
                    </p>

                    <div className="space-y-10">
                        {rulesData.map((section, index) => (
                            <div key={index}>
                                <h2 className="font-semibold text-2xl text-gray-800 mb-3 pb-2 border-b border-gray-200">
                                    {section.title}
                                </h2>
                                <ul className="mt-4 space-y-3 list-inside">
                                    {section.content.map((text, i) => (
                                        <li key={i} className="text-base text-gray-700 leading-relaxed flex items-start">
                                            <span className="text-blue-500 mr-2 mt-1">&#8226;</span>
                                            {text}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        ))}
                    </div>

                    <p className="text-center text-gray-500 text-sm mt-16 pt-8 border-t border-gray-200">
                        Última actualización: 1 de Julio de 2025
                    </p>
                </div>
            </div>
        </Layout>
    );
}

export default Terminos;
