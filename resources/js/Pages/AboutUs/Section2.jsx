import React from "react";
// Eliminado: import { Link } from "@inertiajs/react"; ya que no se usa en esta vista directamente

// Componente TitleSection Simplificado (incrustado para evitar errores de importación)
const TitleSection = ({ title, subTitle, description, className }) => {
    return (
        <div className={`mb-8 ${className}`}>
            {subTitle && (
                <p className="text-sm font-semibold uppercase text-blue-600 mb-2">
                    {subTitle}
                </p>
            )}
            <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                {title}
            </h2>
            {description && (
                <p className="text-md text-gray-600 max-w-2xl mx-auto">
                    {description}
                </p>
            )}
        </div>
    );
};

const Section2 = () => {
    return (
        <section className="bg-gradient-to-br from-indigo-50 to-purple-100 py-16 sm:py-24 overflow-hidden">
            <div className="container mx-auto px-4">
                {/* Título principal de la sección */}
                <TitleSection
                    title="Nuestra Esencia: Misión y Visión"
                    subTitle="DESCUBRE NUESTROS PILARES"
                    description="Conoce el propósito que nos impulsa y la meta que nos guía para ser el mejor parque de trampolines."
                    className="text-center mb-16"
                />

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-start">
                    {/* Tarjeta de Misión */}
                    <div className="bg-white p-8 sm:p-10 rounded-2xl shadow-xl border border-gray-100 transform transition-all duration-300 hover:shadow-2xl hover:border-blue-200">
                        <TitleSection 
                            title="NUESTRA MISIÓN" 
                            subTitle="PROPÓSITO QUE NOS IMPULSA" 
                            description="" 
                            className="text-center mb-6"
                        />
                        <div className="space-y-4 text-lg text-gray-700 leading-relaxed">
                            <p>
                                Ofrecer un espacio de recreación vibrante y seguro en el que nuestro público disfrute de
                                atracciones de primera, donde la diversión, la risa y la alegría sean los
                                protagonistas de cada visita.
                            </p>
                            <p>
                                BRINCA ESTE promete ser un lugar diferente e innovador para compartir momentos inolvidables en familia y amigos, 
                                donde cada uno de nuestros ambientes se transforme en una experiencia memorable y perfecta para repetir una y otra vez.
                            </p>
                        </div>
                    </div>

                    {/* Tarjeta de Visión */}
                    <div className="bg-white p-8 sm:p-10 rounded-2xl shadow-xl border border-gray-100 transform transition-all duration-300 hover:shadow-2xl hover:border-blue-200">
                        <TitleSection 
                            title="NUESTRA VISIÓN" 
                            subTitle="EL FUTURO QUE CONSTRUIMOS" 
                            description="" 
                            className="text-center mb-6"
                        />
                        <div className="space-y-4 text-lg text-gray-700 leading-relaxed">
                            <p>
                                Convertirnos en el líder indiscutible en entretenimiento familiar de
                                Venezuela, siendo el referente de innovación, seguridad y diversión para todas las edades.
                            </p>
                            <p>
                                Aspiramos a expandir nuestra presencia y continuar innovando en experiencias de ocio, manteniendo siempre la excelencia y la satisfacción de nuestros visitantes como pilares fundamentales.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default Section2;

