import type { SharedInertiaProps } from "@/types/modules/support/http/middleware/handle-inertia-requests";

declare module "react" {
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module "@inertiajs/core" {
    export interface InertiaConfig {
        sharedPageProps: SharedInertiaProps & {
            [key: string]: unknown;
        };
    }
}
