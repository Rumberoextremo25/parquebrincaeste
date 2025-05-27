import React from "react";
import Banner from "./Banner";

import Layout from "@/Layouts/Layout";
import Section1 from "./Section1";
import Section2 from "./Section2";
import Section3 from "./Section3";
import Gallery from "./Gallery";
import Birthday from "./birthday";
import BannerHero from "@/Components/Hero/BannerHero";

const AboutUs = () => {
    return (
        <Layout title="Sobre nosotros">
            <BannerHero img="/img/about/BANNER-ABOUT-US.WEBP" title="PARQUE BRINCAESTE" />
            <div className="bg-gray-100">
                <Section1 />
                <Section2 />
                <Section3 />
            </div>
            <div className="bg-gray-100">
                <Gallery />
            </div>
            <Birthday/>
        </Layout>
    );
};

export default AboutUs;
