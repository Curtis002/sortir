const searchInput = document.getElementById('searchRueInput');
const villeInput = document.getElementById('villeInput')


let search = '';


const fetchSearch = async(url) => {
    ville = await fetch(
        `https://api-adresse.data.gouv.fr/search/?q=${url}&type=housenumber&autocomplete=1`)
        .then(res => res.json())


     //curl 'https://api-adresse.data.gouv.fr/search/?q=${url}&type=housenumber&autocomplete=1'
    //        `https://api-adresse.data.gouv.fr/search/?q=${url}&type=municipality&autocomplete=1`)

    //console.log(ville[0]['properties']['postcode']);
    console.log(ville);

};


//search
const searchDisplay = async() => {
    await fetchSearch(search);
    //let villeNom = ville[0]['properties']['postcode'];
    //console.log(villeNom)
    //document.getElementById('villeInput').value = villeNom;



};

searchInput.addEventListener('input', (e) => {
    search = `${e.target.value}`
    searchDisplay();
    console.log(search)
})

fetchSearch();