const searchRueInput = document.getElementById('searchRueInput');
const villeLieuxInput = document.getElementById('villeLieuxInput')


let search = '';


const fetchSearch = async(url) => {
    ville = await fetch(
        `https://api-adresse.data.gouv.fr/search/?q=${url}&type=housenumber&autocomplete=1`)
        .then(res => res.json())
        // gouv     : https://api-adresse.data.gouv.fr/search/?q=${url}&type=housenumber&autocomplete=1
        // gouv     : https://api-adresse.data.gouv.fr/search/?q=${url}&type=municipality&autocomplete=1
        // la poste : https://api.laposte.fr/controladresse/v1/adresses{?q}

    //console.log(ville[0]['properties']['postcode']);
    console.log(ville);

};

//search
const searchDisplay = async() => {
    await fetchSearch(searchRue);

    var villeNom = ville['features'][0]['properties']['city'];
    var rueNom = ville['features'][0]['properties']['name'];

    /*console.log("----valeur api---");
    console.log("nom de la rue rueNom : " + rueNom);
    console.log("nom de la ville api villeNom : " + villeNom);*/

    let selectBox = document.getElementById('villeLieuxInput');

    console.log("----valeur form---");
    var villeSelectionnee = document.getElementById('villeLieuxInput').options[document.getElementById('villeLieuxInput').selectedIndex].text;
    //console.log("nom de la ville selectionnée : " + villeSelectionnee);

    for (var j = 0; j < selectBox.options.length; j++) {

        var texteDuSelect = selectBox.options[j].text;
        //console.log("ville a selectioner dans derniere doucle : "+texteDuSelect);
        //console.log("ville nom api dans derniere doucle : "+villeNom);
        if ( texteDuSelect == villeNom){
            //console.log("activer la case")
            selectBox.options[j].selected = true;
        }
        else
        {
            console.log("test")
        }
    }

};

searchRueInput.addEventListener('input', (e) => {
    searchRue = `${e.target.value}`
    searchDisplay();
})

fetchSearch();