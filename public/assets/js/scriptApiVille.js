const searchInput = document.getElementById('searchInput');
const codePostalField = document.getElementById('codePostalField')


let search = '';


const fetchSearch = async(url) => {
    ville = await fetch(
        `https://api-adresse.data.gouv.fr/search/?q=${url}&type=municipality&autocomplete=1`)
        .then(res => res.json())
        .then(res => res.features)

     //'https://geo.api.gouv.fr/communes?nom=${url}&fields=code,nom,centre,codesPostaux'
    //        `https://api-adresse.data.gouv.fr/search/?q=${url}&type=municipality&autocomplete=1`)

    //console.log(ville[0]['properties']['postcode']);

};


//search
const searchDisplay = async() => {
    await fetchSearch(search);
    let cp = ville[0]['properties']['postcode'];
    //console.log(cp)
    document.getElementById('codePostalField').value = cp;



};

searchInput.addEventListener('input', (e) => {
    search = `${e.target.value}`
    searchDisplay();
    console.log(search)
})

fetchSearch();