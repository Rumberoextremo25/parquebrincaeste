import Card from '@/Components/Card' // Asumiendo que esta es la ruta correcta
import BannerHero from '@/Components/Hero/BannerHero' // Asumiendo que esta es la ruta correcta
import ListDescription from '@/Components/ListDescription' // Asumiendo que esta es la ruta correcta
import TitleSection from '@/Components/TitleSection' // Asumiendo que esta es la ruta correcta
import Layout from '@/Layouts/Layout' // Asumiendo que esta es la ruta correcta
import React from 'react'
import CardsInformation from './CardsInformation' // Asumiendo que está en el mismo directorio
import FormContact from './FormContact' // Asumiendo que está en el mismo directorio

const ContactUs = () => {
    return (
        <Layout title="Contáctenos">
            {/* Sección de Banner Hero */}
            <BannerHero
                title="CONTACTA CON NOSOTROS"
                desc="Estamos aquí para ayudarte. Si tienes alguna pregunta, sugerencia o necesitas asistencia, no dudes en comunicarte con nuestro equipo."
                //img="/img/about/IMG_2748.PNG" // Puedes cambiar esta imagen por una más adecuada para Contacto
            />

            <div className="container mx-auto p-4 py-16 sm:py-24"> {/* Contenedor principal con más padding vertical */}

                {/* Sección de Información de Contacto */}
                <section className="mb-16">
                    <TitleSection
                        className="text-center mb-12"
                        title="Nuestra Información de Contacto"
                        subTitle="¿CÓMO PODEMOS AYUDARTE?"
                        description="Encuentra todas las formas de ponerte en contacto con nosotros para cualquier consulta o soporte."
                    />
                    <CardsInformation /> {/* Componente que contiene las tarjetas de información */}
                </section>

                {/* Sección del Mapa de Ubicación */}
                <section className="mb-16">
                    <TitleSection
                        className="text-center mb-12"
                        title="Encuéntranos Aquí"
                        subTitle="NUESTRA UBICACIÓN"
                        description="Visítanos en nuestra sede. Estamos ubicados en el corazón de la diversión."
                    />
                    <div className="relative w-full rounded-2xl overflow-hidden shadow-2xl border border-gray-100 transition-all duration-300 hover:shadow-3xl hover:border-blue-200">
                        <iframe
                            loading="lazy"
                            className="w-full h-[400px] sm:h-[500px] border-0" // Altura adaptable y sin borde (gestionado por Tailwind)
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3923.118420498544!2d-66.83895122519934!3d10.491330089640654!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8c2a59442fe13d6b%3A0xca378303a907de38!2sBrinca%20Este!5e0!3m2!1ses-419!2sve!4v1751164737400!5m2!1ses-419!2sve"
                            allowFullScreen=""
                            aria-hidden="false"
                            tabIndex="0"
                            // `referrerpolicy="no-referrer-when-downgrade"` es un atributo HTML válido y se mantiene.
                            referrerPolicy="no-referrer-when-downgrade" 
                        ></iframe>
                        {/* Overlay sutil para el mapa (opcional, para efecto visual) */}
                        <div className="absolute inset-0 bg-gradient-to-t from-blue-50 to-transparent opacity-30"></div>
                    </div>
                    <p className="text-center text-gray-600 mt-4 text-sm">
                        Haz clic en el mapa para ver la ubicación en Google Maps.
                    </p>
                </section>

                {/* Sección del Formulario de Contacto (con el rediseño anterior) */}
                <section>
                    {/* El TitleSection y el fondo ya están dentro de FormContact, así que no se repiten aquí */}
                    <FormContact />
                </section>

            </div>
        </Layout>
    )
}

export default ContactUs
