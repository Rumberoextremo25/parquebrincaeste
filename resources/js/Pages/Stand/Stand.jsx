import React from 'react'
import FrequentlyAsked from "@/Components/FrequentlyAsked";
import Layout from "@/Layouts/Layout";
import BannerHero from '@/Components/Hero/BannerHero';

const Stand = () => {
    return (
        <Layout>
            <BannerHero title="FERIA DE COMIDA" />
            <div className="py-section container">
                {[
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
                        description: "Vive la magia de Disney con los increíbles bolsos y morrales del maravilloso mundo de Disney.",
                        imgSrc: "/img/stand/Fantasy.png",
                        imgAlt: "Disney Fantasy",
                        isImageFirst: false
                    },
                    {
                        title: "DUTCH PANCAKES",
                        description: "Deléitate en Dutch Pancakes con nuestros irresistibles pancakes, perfectos para cualquier momento del día. Acompáñalos con nuestros aromáticos cafés o refrescantes smoothies.",
                        imgSrc: "/img/stand/IMG_2751.png",
                        imgAlt: "Dutch Pancakes",
                        isImageFirst: true
                    },
                    {
                        title: "Brinca Burger",
                        description: "Disfruta de las deliciosas hamburguesas que te ofrecemos en Brinca Burger, el mejor sabor y sazón.",
                        imgSrc: "/img/stand/brincaburger.png",
                        imgAlt: "Brinca Burger",
                        isImageFirst: false
                    }
                ].map((stand, index) => (
                    <div key={index} className="grid gap-24 lg:grid-cols-2">
                        {stand.isImageFirst && (
                            <div className="hidden lg:block">
                                <img
                                    src={stand.imgSrc}
                                    alt={stand.imgAlt}
                                    className="object-lefts h-full object-cover"
                                />
                            </div>
                        )}
                        <div>
                            <strong><h5>{stand.title}</h5></strong>
                            <div className="mt-4 space-y-6">
                                <p className="text">{stand.description}</p>
                                {stand.additional && <p className="text">{stand.additional}</p>}
                            </div>
                        </div>
                        {!stand.isImageFirst && (
                            <div className="hidden lg:block">
                                <img
                                    src={stand.imgSrc}
                                    alt={stand.imgAlt}
                                    className="object-lefts h-full object-cover"
                                />
                            </div>
                        )}
                    </div>
                ))}
            </div>
        </Layout>
    )
}

export default Stand