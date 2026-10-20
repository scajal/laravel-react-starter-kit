import type { Auth } from "@/types/auth";
import { SharedInertiaProps } from "./modules/shared/inertia-shared";

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
