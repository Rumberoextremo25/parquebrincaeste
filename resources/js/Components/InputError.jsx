export default function InputError({ message, className = '', ...props }) {
    return message ? (
        <p {...props} className={'text-sm text-indigo-600 ' + className}>
            {message}
        </p>
    ) : null;
}
