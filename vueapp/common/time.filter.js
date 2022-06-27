import { format } from 'date-fns'
import { de } from 'date-fns/locale'

export default date => {
    if (date === null) {
        return '';
    }

    return format(new Date(date), "HH:mm", { locale: de});
};
