import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import { toast } from 'sonner';

interface FlashMessages {
    success?: string;
    error?: string;
}

export function useFlashMessages() {
    const page = usePage<{ flash?: FlashMessages }>();

    useEffect(() => {
        const flash = page.props.flash;
        if (flash?.success) {
            toast.success(flash.success);
        }
        if (flash?.error) {
            toast.error(flash.error);
        }
    }, [page.props]);
}



