import React from 'react';
import FrequentlyAsked from "@/Components/FrequentlyAsked"; // Usando alias @/
import Layout from "@/Layouts/Layout"; // Usando alias @/
import BannerHero from '@/Components/Hero/BannerHero'; // Usando alias @/
import TitleSection from '@/Components/TitleSection'; // Usando alias @/

const Faq = () => {
    return (
        <Layout title="Preguntas Frecuentes">
            {/* Sección de Banner Hero */}
            <BannerHero
                title="PREGUNTAS FRECUENTES"
                desc="Encuentra respuestas rápidas a las dudas más comunes sobre nuestro parque, horarios, métodos de pago y más."
                //img="/img/home/IMG_9783.jpg" // Puedes usar una imagen más representativa si lo deseas
            />

            <div className="container mx-auto p-4 py-16 sm:py-24"> {/* Contenedor principal con padding responsivo */}
                
                {/* Sección de Título para FAQs */}
                <TitleSection
                    className="text-center mb-12 lg:mb-16"
                    title="Todo lo que Necesitas Saber"
                    subTitle="RESOLVIENDO TUS DUDAS"
                    description="Aquí respondemos a las preguntas más frecuentes para que tu experiencia en Brinca Este sea lo más clara y divertida posible."
                />

                {/* Contenedor de las Preguntas Frecuentes */}
                <div className="bg-white p-6 sm:p-8 rounded-2xl shadow-xl border border-gray-100 space-y-6">
                    <FrequentlyAsked question="¿Cuál es su Horario Laboral?">
                        <p className="text-gray-700 leading-relaxed">
                            <strong>Martes a Jueves:</strong><br />
                            11:00 AM - 8:00 PM
                        </p>
                        <p className="text-gray-700 leading-relaxed mt-2">
                            <strong>Viernes, Sábado y Domingo:</strong><br />
                            11:00 AM - 9:00 PM
                        </p>
                    </FrequentlyAsked>

                    <FrequentlyAsked question="¿Dónde están ubicados?">
                        <p className="text-gray-700 leading-relaxed">
                            Estamos ubicados en la Avenida Francisco de Miranda, estacionamiento 2 del Parque Generalísimo Francisco de Miranda (Parque del Este) diagonal al Museo de Transporte, Caracas, Venezuela.
                        </p>
                    </FrequentlyAsked>

                    <FrequentlyAsked question="¿Cuáles son sus métodos de pago?">
                        <p className="text-gray-700 leading-relaxed">
                            Aceptamos pagos en Efectivo, Punto de venta, Pago Móvil o Zelle. ¡Facilitamos tus transacciones para que disfrutes sin preocupaciones!
                        </p>
                    </FrequentlyAsked>

                    <FrequentlyAsked question="¿Cuál es el rango de edad para usar el parque?">
                        <p className="text-gray-700 leading-relaxed">
                            <strong>Parque Infantil:</strong> De 10 meses a 5 años
                        </p>
                        <p className="text-gray-700 leading-relaxed mt-2">
                            <strong>Ninja Park:</strong> De 6 a 12 años
                        </p>
                        <p className="text-gray-700 leading-relaxed mt-2">
                            <strong>Otras áreas:</strong> De 6 a 65 años
                        </p>
                    </FrequentlyAsked>

                    <FrequentlyAsked question="¿Puedo realizar fiestas o eventos?">
                        <p className="text-gray-700 leading-relaxed">
                            Sí, contamos con 3 planes exclusivos para que realices tus eventos en nuestro parque. Para más información y personalizar tu celebración, contáctanos al
                            <br />
                            <strong>+58 412 3508826</strong>. ¡Hacemos de tu evento un momento inolvidable!
                        </p>
                    </FrequentlyAsked>
                </div>

            </div>
        </Layout>
    )
}

export default Faq;