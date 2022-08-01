import { createStore } from 'vuex';

import error        from "./error.module";
import messages     from "./messages.module";
import course       from "./course.module";

export default createStore({
  modules: {
    error,    messages,   course
  }
});
