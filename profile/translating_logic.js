//to detect the text lang
import { franc } from 'https://cdn.jsdelivr.net/npm/franc@6/+esm';

//the function which is used to translate the post_caption from english to arabic
async function translateText(text) {
    try {
        const response = await fetch(`https://api.mymemory.translated.net/get?q=${encodeURIComponent(text)}&langpair=en|ar`);
        const data = await response.json();
        return data.responseData.translatedText;//from the api
    } catch (error) {
        window.alert('Translation Faild :', error);
    }
}


const spanOfTranslation=document.getElementById('translate');
const translatedCaption=document.getElementById('translated-caption');
spanOfTranslation.addEventListener('click',async function(){

    translatedCaption.textContent = await translateText(spanOfTranslation.getAttribute('data-caption'));
    const isVisible = translatedCaption.style.display === 'block';

    //toggle functionality
    if(isVisible===true) {
        spanOfTranslation.textContent='See Translation';
         translatedCaption.style.display = 'none';
    }
    else   {
        spanOfTranslation.textContent='Hide Translation';
        translatedCaption.style.display = 'block';
    }

})



//i modify the code to support short captions
function detectLanguage(text) {
    // franc detects language from text

    const cleanText = text.trim();

    // Use Franc with specific options for better accuracy
    const langCode = franc(cleanText, {
        minLength: 1,
        only: ['arb', 'eng'], // Limit to common languages
        whitelist: ['arb', 'eng'] // Only consider Arabic and English
    });

    return langCode;
}



document.addEventListener('DOMContentLoaded', function() {
    const caption=document.getElementById('modalCaption').textContent;
    const caption_lang=detectLanguage(caption);
    const translation_link=document.getElementById('translationLink');

    if (caption_lang === 'eng') {

        translation_link.classList.add('show-translation-link');
        translation_link.classList.remove('hide-translation-link');

    }
    else  if (caption_lang === 'arb') {

         translation_link.classList.add('hide-translation-link');
         translation_link.classList.remove('show-translation-link');

    }
});
