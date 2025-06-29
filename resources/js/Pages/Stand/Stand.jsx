import React from 'react';
// Usando los aliases (@/) para componentes compartidos.
// Asegúrate de que estos aliases estén configurados en tu vite.config.js o jsconfig.json.
// Por ejemplo, en vite.config.js: resolve: { alias: { '@/': '/resources/js/' } }
import FrequentlyAsked from "@/Components/FrequentlyAsked"; 
import Layout from "@/Layouts/Layout"; 
import BannerHero from '@/Components/Hero/BannerHero'; 

const Stand = () => {
    const standsData = [
        {
            title: "BRINCA MANIA",
            description: "Descubre los sabores de Brinca Manía, donde cada bocado es una aventura. Desde pizzas y perros calientes hasta cotufas y helados, pasando por donas, algodón de azúcar, nuggets, tequeños y papas fritas. ¡Ven y disfruta de nuestras delicias culinarias!",
            additional: "También endulza tu día en el paraíso de las golosinas. Descubre nuestra selección de chucherías, gomitas, y bolsas de papitas crujientes. ¡Un festín de sabores y diversión te espera en cada visita!",
            imgSrc: "/img/stand/IMG_2756.png",
            imgAlt: "Brinca Manía",
            isImageFirst: true
        },
        {
            title: "DISNEY FANTASY",
            description: "Vive la magia de Disney con los increíbles bolsos y morrales del maravilloso mundo de Disney. Encuentra el accesorio perfecto para llevar la fantasía contigo a todas partes.",
            imgSrc: "/img/stand/Fantasy.png",
            imgAlt: "Disney Fantasy",
            isImageFirst: false
        },
        {
            title: "DUTCH PANCAKES",
            description: "Deléitate en Dutch Pancakes con nuestros irresistibles pancakes, perfectos para cualquier momento del día. Acompáñalos con nuestros aromáticos cafés o refrescantes smoothies. Una experiencia culinaria única te espera.",
            imgSrc: "/img/stand/IMG_2751.png",
            imgAlt: "Dutch Pancakes",
            isImageFirst: true
        },
        {
            title: "Brinca Burger",
            description: "Disfruta de las deliciosas hamburguesas que te ofrecemos en Brinca Burger, el mejor sabor y sazón. Preparamos cada hamburguesa con ingredientes frescos y el toque secreto de Brinca Este para garantizar una explosión de sabor en cada mordisco.",
            imgSrc: "/img/stand/brincaburger.png",
            imgAlt: "Brinca Burger",
            isImageFirst: false
        }
    ];

    return (
        <Layout title="Feria de Comida">
            <BannerHero
                title="FERIA "
                desc="Explora nuestra increíble Feria, donde la diversión y el buen gusto se encuentran."
                //img="/img/stand/banner_food_fair.jpg" // Puedes usar una imagen más representativa para el banner de la feria
            />

            <div className="container mx-auto p-4 py-16 sm:py-24"> {/* Contenedor principal con padding responsivo */}

                {standsData.map((stand, index) => (
                    <section
                        key={index}
                        className={`mb-20 last:mb-0 p-8 rounded-3xl shadow-xl border border-gray-100 transition-all duration-300 transform hover:scale-[1.01] ${
                            stand.isImageFirst ? 'bg-white' : 'bg-blue-50' // Alternar colores de fondo
                        }`}
                    >
                        <div className={`flex flex-col lg:flex-row items-center gap-10 ${!stand.isImageFirst ? 'lg:flex-row-reverse' : ''}`}>
                            {/* Columna de Imagen */}
                            <div className="flex-1 w-full lg:w-1/2">
                                <img
                                    src={stand.imgSrc}
                                    alt={stand.imgAlt}
                                    className="w-full h-80 sm:h-96 object-cover rounded-2xl shadow-lg transition-transform duration-300 hover:scale-105"
                                    loading="lazy"
                                    onError={(e) => { e.target.onerror = null; e.target.src = "https://placehold.co/600x400/e0e0e0/000000?text=Imagen+No+Disponible"; }} // Fallback
                                />
                            </div>

                            {/* Columna de Contenido */}
                            <div className="flex-1 w-full lg:w-1/2 text-center lg:text-left">
                                <h3 className="text-4xl font-bold text-gray-900 mb-4 leading-tight">
                                    {stand.title}
                                </h3>
                                <p className="text-lg text-gray-700 mb-4 leading-relaxed">
                                    {stand.description}
                                </p>
                                {stand.additional && (
                                    <p className="text-md text-gray-600 italic leading-relaxed">
                                        {stand.additional}
                                    </p>
                                )}
                            </div>
                        </div>
                    </section>
                ))}
            </div>
        </Layout>
    );
};

export default Stand;