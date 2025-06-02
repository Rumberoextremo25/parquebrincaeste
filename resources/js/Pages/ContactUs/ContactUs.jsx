import Card from '@/Components/Card'
import BannerHero from '@/Components/Hero/BannerHero'
import ListDescription from '@/Components/ListDescription'
import TitleSection from '@/Components/TitleSection'
import Layout from '@/Layouts/Layout'
import React from 'react'
import CardsInformation from './CardsInformation'
import FormContact from './FormContact'

const ContactUs = () => {
    return (
        <Layout title="Contáctenos">
            <BannerHero title="CONTACTANOS" />
            <div className="container mx-auto p-4">

                <div className="py-section">
                    <CardsInformation />
                </div>

                <iframe
                    loading="lazy"
                    className="w-full h-96 my-4 rounded-lg shadow-md"
                    src="https://maps.google.com/maps?q=10.4913301,-66.8389512&hl=es;z=16&output=embed">
                </iframe>

                <div className="py-section">
                    <FormContact />
                </div>

            </div>
        </Layout>
    )
}

export default ContactUs
