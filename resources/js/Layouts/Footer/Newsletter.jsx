import React, { useState } from "react";  
import axios from 'axios';  
import Button from "@/Components/Button";  

const Newsletter = () => {  
    const [email, setEmail] = useState("");  
    const [loading, setLoading] = useState(false);  
    const [message, setMessage] = useState("");  
    const [error, setError] = useState(false);  

    const handleSubmit = async (e) => {  
        e.preventDefault();  
        setLoading(true);  
        setMessage("");  
        setError(false);  

        try {  
            const response = await axios.post("/web/newsletter", { email });  
            if (response.status === 200) {  
                setMessage("¡Gracias por suscribirte!");  
                setEmail("");  
            }  
        } catch (error) {  
            console.error("Error al suscribirse:", error);  
            setMessage("Disculpa, pero no pudimos suscribirte. Inténtalo más tarde.");  
            setError(true);  
        } finally {  
            setLoading(false);  
        }  
    };  

    return (  
        <div className="relative overflow-hidden rounded-3xl py-8 px-4 md:py-12 md:px-5">  
            <div className="relative z-10 mx-auto max-w-2xl text-center">  
                <p className="text-lg font-medium uppercase text-white md:text-2xl">  
                    Suscríbete  
                </p>  
                <h3 className="mt-4 text-2xl font-bold uppercase md:text-4xl text-white">  
                    PARA OBTENER BENEFICIOS EXCLUSIVOS  
                </h3>  
                <form onSubmit={handleSubmit} className="bg-white mt-12 flex h-12 w-full items-stretch rounded-full border border-white border-opacity-30 pl-5 shadow">  
                    <input  
                        type="email"  
                        value={email}  
                        onChange={(e) => setEmail(e.target.value)}  
                        className="w-full grow border-none bg-inherit placeholder:text-gray-300 focus:ring-0"  
                        placeholder="Email"  
                        required  
                    />  
                    <Button className="rounded-full" type="submit" disabled={loading}>  
                        {loading ? 'Cargando...' : 'Suscribirse'}  
                    </Button>  
                </form>  
                {message && (  
                    <p className={`mt-4 text-sm ${error ? 'text-white-500' : 'text-green-500'}`}>  
                        {message}  
                    </p>  
                )}  
                <p className="mt-8 text-sm text-white">  
                    Respetamos su privacidad, por lo que nunca compartiremos su información.  
                </p>  
            </div>  
            <div className="absolute inset-0 bg-gradient-to-r from-violet-500 to-violet-600"></div>  
        </div>  
    );  
};  

export default Newsletter;
